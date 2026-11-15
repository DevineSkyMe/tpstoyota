<?php

namespace App\Http\Controllers;

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
     * Send the workshop report to HRD and/or Manager.
     */
    public function send(Request $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validate([
            'target' => ['required', 'in:hrd,manager,both'],
        ]);

        $report = WorkshopScoreReport::firstOrCreate(
            ['workshop_id' => $workshop->id],
            [
                'generated_by' => $request->user()->id,
                'pdf_path' => route('reports.workshop', $workshop),
                'generated_at' => now(),
                'status' => 'generated',
            ]
        );

        $roles = match ($validated['target']) {
            'hrd' => [UserAccount::ROLE_HRD],
            'manager' => [UserAccount::ROLE_MANAGER],
            default => [UserAccount::ROLE_HRD, UserAccount::ROLE_MANAGER],
        };

        $recipients = UserAccount::whereIn('role', $roles)->get();

        if ($recipients->isEmpty()) {
            return back()->with('error', 'Akun penerima untuk role tersebut belum tersedia.');
        }

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

            // Deliver a real email when a mail transport is configured; always keep the in-app inbox.
            if ($recipient->email) {
                try {
                    Mail::raw(
                        "Laporan Hasil {$workshop->title}\n\n" . route('reports.workshop', $workshop),
                        fn ($message) => $message->to($recipient->email)->subject('Laporan Hasil ' . $workshop->title)
                    );
                } catch (\Throwable $exception) {
                    report($exception);
                }
            }

            if ($recipient->isHrd()) {
                $report->sent_to_hrd_at = now();
            }

            if ($recipient->isManager()) {
                $report->sent_to_director_at = now();
            }
        }

        $report->status = 'completed';
        $report->save();

        return back()->with('success', 'Laporan berhasil dikirim ke ' . $recipients->map->role_label->join(' & ') . '.');
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
