<?php

namespace App\Http\Controllers;

use App\Mail\ActivityReportMail;
use App\Mail\WorkshopReportMail;
use App\Models\ActivityPlan;
use App\Models\Department;
use App\Models\Message;
use App\Models\UserAccount;
use App\Models\Workshop;
use App\Models\WorkshopEvaluation;
use App\Models\WorkshopScoreReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Report hub for Admin & Manager.
     */
    public function index(): View
    {
        $workshops = Workshop::withCount(['participants', 'evaluations'])
            ->with('scoreReports')
            ->orderByDesc('year')
            ->orderByDesc('batch')
            ->get();

        $activityStats = [
            'total' => ActivityPlan::count(),
            'in_progress' => ActivityPlan::where('status', 'in_progress')->count(),
            'completed' => ActivityPlan::where('status', 'completed')->count(),
            'delayed' => ActivityPlan::where('status', 'delay')->count(),
        ];

        $reportsSent = Message::where('related_type', 'workshop_score_report')->count();

        return view('reports.index', compact('workshops', 'activityStats', 'reportsSent'));
    }

    /**
     * Automatically generated activity report.
     */
    public function activity(Request $request): View
    {
        [$query, $filters] = $this->activityQuery($request);

        $activities = $query->with(['employee.plant', 'employee.department'])->latest()->get();

        $summary = [
            'total' => $activities->count(),
            'completed' => $activities->where('status', 'completed')->count(),
            'in_progress' => $activities->where('status', 'in_progress')->count(),
            'delayed' => $activities->where('status', 'delay')->count(),
            'not_started' => $activities->where('status', 'not_started')->count(),
            'avg_progress' => $activities->count() ? round($activities->avg('progress_pct'), 1) : 0,
        ];

        $departments = Department::orderBy('name')->get();

        return view('reports.activity', compact('activities', 'summary', 'filters', 'departments'));
    }

    /**
     * Download the activity report as CSV.
     */
    public function exportActivity(Request $request): StreamedResponse
    {
        [$query, $filters] = $this->activityQuery($request);
        $activities = $query->with(['employee.department', 'employee.plant'])->orderBy('id')->get();

        $filename = 'activity-report-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($activities) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Judul', 'Kategori', 'Karyawan', 'NIK', 'Plant', 'Departemen', 'Mulai', 'Selesai', 'Status', 'Progress (%)']);

            foreach ($activities as $activity) {
                fputcsv($out, [
                    $activity->id,
                    $activity->title,
                    $activity->category,
                    $activity->employee?->name,
                    $activity->employee?->username,
                    $activity->employee?->plant?->name,
                    $activity->employee?->department?->name,
                    optional($activity->planned_start)->format('Y-m-d'),
                    optional($activity->planned_end)->format('Y-m-d'),
                    $activity->status,
                    $activity->progress_pct,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Workshop result report (score recap + pass/fail).
     */
    public function workshop(Workshop $workshop): View
    {
        $workshop->load([
            'participants.employee.plant',
            'participants.employee.department',
            'participants.evaluation',
            'scoreReports.generator',
        ]);

        $evaluations = $workshop->participants
            ->map(fn ($participant) => $participant->evaluation)
            ->filter();

        $summary = [
            'participants' => $workshop->participants->count(),
            'evaluated' => $evaluations->count(),
            'passed' => $evaluations->filter(fn ($evaluation) => $evaluation->isPassed())->count(),
            'failed' => $evaluations->filter(fn ($evaluation) => ! $evaluation->isPassed())->count(),
            'average' => $evaluations->count() ? round($evaluations->avg('composite_score'), 1) : 0,
        ];

        $report = $workshop->scoreReports->sortByDesc('id')->first();
        $canSend = request()->user()->isAdmin();

        return view('reports.workshop', compact('workshop', 'summary', 'report', 'canSend'));
    }

    /**
     * Generate (or refresh) the workshop score report record.
     */
    public function generate(Request $request, Workshop $workshop): RedirectResponse
    {
        $report = WorkshopScoreReport::updateOrCreate(
            ['workshop_id' => $workshop->id],
            [
                'generated_by' => $request->user()->id,
                'pdf_path' => route('reports.workshop', $workshop),
                'generated_at' => now(),
                'status' => 'generated',
            ]
        );

        return redirect()->route('reports.workshop', $workshop)
            ->with('success', "Laporan hasil {$workshop->title} berhasil digenerate (#{$report->id}).");
    }

    /**
     * Send the workshop report to HRD, Manager, and/or a custom email.
     */
    public function send(Request $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validate([
            'target' => ['required', 'in:hrd,manager,both,custom'],
            'custom_email' => ['nullable', 'email'],
        ]);

        if ($validated['target'] === 'custom' && empty($validated['custom_email'])) {
            return back()->with('error', 'Masukkan alamat email tujuan pengiriman laporan.');
        }

        $workshop->load([
            'participants.employee.plant',
            'participants.employee.department',
            'participants.evaluation',
        ]);

        $evaluations = $workshop->participants
            ->map(fn ($participant) => $participant->evaluation)
            ->filter();

        $summary = [
            'participants' => $workshop->participants->count(),
            'evaluated' => $evaluations->count(),
            'passed' => $evaluations->filter(fn ($evaluation) => $evaluation->isPassed())->count(),
            'failed' => $evaluations->filter(fn ($evaluation) => ! $evaluation->isPassed())->count(),
            'average' => $evaluations->count() ? round($evaluations->avg('composite_score'), 1) : 0,
        ];

        $report = WorkshopScoreReport::firstOrCreate(
            ['workshop_id' => $workshop->id],
            [
                'generated_by' => $request->user()->id,
                'pdf_path' => route('reports.workshop', $workshop),
                'generated_at' => now(),
                'status' => 'generated',
            ]
        );

        $sentRecipients = [];
        $failedRecipients = [];

        // 1. Send to role-based system accounts if requested
        if ($validated['target'] !== 'custom') {
            $roles = match ($validated['target']) {
                'hrd' => [UserAccount::ROLE_HRD],
                'manager' => [UserAccount::ROLE_MANAGER],
                default => [UserAccount::ROLE_HRD, UserAccount::ROLE_MANAGER],
            };

            $recipients = UserAccount::whereIn('role', $roles)->get();

            foreach ($recipients as $recipient) {
                $alreadySent = Message::where('sender_id', $request->user()->id)
                    ->where('receiver_id', $recipient->id)
                    ->where('related_type', 'workshop_score_report')
                    ->where('related_id', $report->id)
                    ->exists();

                if (! $alreadySent) {
                    Message::create([
                        'sender_id' => $request->user()->id,
                        'receiver_id' => $recipient->id,
                        'related_type' => 'workshop_score_report',
                        'related_id' => $report->id,
                        'message' => "Laporan hasil {$workshop->title} telah diterbitkan. Silakan buka detail laporan untuk melihat nilai dan keputusan kelulusan peserta.",
                        'attachment_path' => $report->pdf_path,
                        'is_read' => false,
                    ]);
                }

                if ($recipient->email) {
                    // Jika menggunakan domain lokal dummy (.local), lewati SMTP agar Gmail tidak mereject domain yang tidak ada
                    if (str_ends_with(strtolower($recipient->email), '.local')) {
                        $sentRecipients[] = "{$recipient->role_label} (Inbox Portal)";
                    } else {
                        try {
                            Mail::to($recipient->email)->send(new WorkshopReportMail($workshop, $summary, $recipient, $report->pdf_path));
                            $sentRecipients[] = "{$recipient->role_label} ({$recipient->email})";
                        } catch (\Throwable $exception) {
                            report($exception);
                            $failedRecipients[] = "{$recipient->role_label} ({$recipient->email}): " . $exception->getMessage();
                        }
                    }
                }

                if ($recipient->isHrd()) {
                    $report->sent_to_hrd_at = now();
                }

                if ($recipient->isManager()) {
                    $report->sent_to_director_at = now();
                }
            }
        }

        // 2. Send to custom email if provided
        if (! empty($validated['custom_email'])) {
            try {
                Mail::to($validated['custom_email'])->send(new WorkshopReportMail($workshop, $summary, null, $report->pdf_path));
                $sentRecipients[] = $validated['custom_email'];
            } catch (\Throwable $exception) {
                report($exception);
                $failedRecipients[] = "{$validated['custom_email']}: " . $exception->getMessage();
            }
        }

        $report->status = 'completed';
        $report->save();

        if (empty($sentRecipients) && ! empty($failedRecipients)) {
            return back()->with('error', 'Gagal mengirim email: ' . implode(' | ', $failedRecipients));
        }

        $msg = 'Laporan hasil ' . $workshop->title . ' berhasil dikirim ke ' . implode(', ', $sentRecipients) . '.';
        if (! empty($failedRecipients)) {
            $msg .= ' (Sebagian gagal: ' . implode(' | ', $failedRecipients) . ')';
        }

        return back()->with('success', $msg);
    }

    /**
     * Send the activity report as PDF to HRD, Manager, or a custom email.
     */
    public function sendActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target'       => ['required', 'in:hrd,manager,both,custom'],
            'custom_email' => ['nullable', 'email'],
            'start_date'   => ['nullable', 'date'],
            'end_date'     => ['nullable', 'date'],
            'department_id'=> ['nullable', 'integer'],
            'status'       => ['nullable', 'string'],
        ]);

        if ($validated['target'] === 'custom' && empty($validated['custom_email'])) {
            return back()->with('error', 'Masukkan alamat email tujuan pengiriman laporan.');
        }

        // Re-build the same query used by the activity() method
        $filters = [
            'start_date'    => $validated['start_date']    ?? now()->startOfYear()->toDateString(),
            'end_date'      => $validated['end_date']      ?? now()->endOfYear()->toDateString(),
            'department_id' => $validated['department_id'] ?? null,
            'status'        => $validated['status']        ?? null,
        ];

        $activities = ActivityPlan::query()
            ->when($filters['start_date'],    fn ($q, $d)  => $q->whereDate('planned_start', '>=', $d))
            ->when($filters['end_date'],      fn ($q, $d)  => $q->whereDate('planned_start', '<=', $d))
            ->when($filters['department_id'], fn ($q, $id) => $q->whereHas('employee', fn ($e) => $e->where('department_id', $id)))
            ->when($filters['status'],        fn ($q, $s)  => $q->where('status', $s))
            ->with(['employee.plant', 'employee.department'])
            ->latest()
            ->get();

        $summary = [
            'total'       => $activities->count(),
            'completed'   => $activities->where('status', 'completed')->count(),
            'in_progress' => $activities->where('status', 'in_progress')->count(),
            'delayed'     => $activities->where('status', 'delay')->count(),
            'not_started' => $activities->where('status', 'not_started')->count(),
            'avg_progress'=> $activities->count() ? round($activities->avg('progress_pct'), 1) : 0,
        ];

        $portalUrl      = route('reports.activity');
        $sentRecipients = [];
        $failedRecipients = [];

        $sendTo = function (string $email, string $label) use ($activities, $summary, $filters, $portalUrl, &$sentRecipients, &$failedRecipients) {
            if (str_ends_with(strtolower($email), '.local')) {
                $sentRecipients[] = "{$label} (Inbox Portal saja)";
                return;
            }
            try {
                Mail::to($email)->send(new ActivityReportMail($activities, $summary, $filters, $portalUrl));
                $sentRecipients[] = "{$label} ({$email})";
            } catch (\Throwable $e) {
                report($e);
                $failedRecipients[] = "{$label} ({$email}): " . $e->getMessage();
            }
        };

        if ($validated['target'] !== 'custom') {
            $roles = match ($validated['target']) {
                'hrd'    => [UserAccount::ROLE_HRD],
                'manager'=> [UserAccount::ROLE_MANAGER],
                default  => [UserAccount::ROLE_HRD, UserAccount::ROLE_MANAGER],
            };

            foreach (UserAccount::whereIn('role', $roles)->get() as $recipient) {
                if ($recipient->email) {
                    $sendTo($recipient->email, $recipient->role_label);
                }
            }
        }

        if (! empty($validated['custom_email'])) {
            $sendTo($validated['custom_email'], 'Custom');
        }

        if (empty($sentRecipients) && ! empty($failedRecipients)) {
            return back()->with('error', 'Gagal mengirim email: ' . implode(' | ', $failedRecipients));
        }

        $msg = 'Laporan Activity berhasil dikirim ke ' . implode(', ', $sentRecipients) . '.';
        if (! empty($failedRecipients)) {
            $msg .= ' (Sebagian gagal: ' . implode(' | ', $failedRecipients) . ')';
        }

        return back()->with('success', $msg);
    }

    /**
     * Shared activity report query + filter state.
     */
    private function activityQuery(Request $request): array
    {
        $filters = [
            'start_date' => $request->input('start_date', now()->startOfYear()->toDateString()),
            'end_date' => $request->input('end_date', now()->endOfYear()->toDateString()),
            'department_id' => $request->input('department_id'),
            'status' => $request->input('status'),
        ];

        $query = ActivityPlan::query()
            ->when($filters['start_date'], fn ($q, $date) => $q->whereDate('planned_start', '>=', $date))
            ->when($filters['end_date'], fn ($q, $date) => $q->whereDate('planned_start', '<=', $date))
            ->when($filters['department_id'], fn ($q, $id) => $q->whereHas('employee', fn ($e) => $e->where('department_id', $id)))
            ->when($filters['status'], fn ($q, $status) => $q->where('status', $status));

        return [$query, $filters];
    }
}
