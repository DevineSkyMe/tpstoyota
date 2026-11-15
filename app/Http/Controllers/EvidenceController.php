<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopEvidence;
use App\Models\WorkshopParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    /**
     * A participant uploads the weekly proof of attendance for a workshop
     * they are enrolled in.
     */
    public function store(Request $request, Workshop $workshop): RedirectResponse
    {
        $user = $request->user();

        $participant = WorkshopParticipant::where('workshop_id', $workshop->id)
            ->where('employee_id', $user->employee_id)
            ->first();

        abort_unless(
            $participant && $participant->confirmation_status === 'confirmed',
            403,
            'Anda hanya dapat mengirim bukti untuk workshop yang Anda ikuti.'
        );

        $validated = $request->validate([
            'week_no' => ['required', 'integer', 'min:1', 'max:52'],
            'title' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string', 'max:1000'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx', 'max:5120'],
        ]);

        $hasFile = $request->hasFile('evidence');
        $hasLink = ! empty($validated['link_url']);
        $hasNote = ! empty($validated['note']);

        if (! $hasFile && ! $hasLink && ! $hasNote) {
            return back()
                ->withErrors(['evidence' => 'Lampirkan minimal satu bukti: file, tautan, atau catatan.'])
                ->withInput();
        }

        $attributes = [
            'title' => $validated['title'] ?? null,
            'note' => $validated['note'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'status' => WorkshopEvidence::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'reviewed_by' => null,
            'reviewed_at' => null,
            'review_note' => null,
        ];

        if ($hasFile) {
            $file = $request->file('evidence');
            $directory = public_path('uploads/workshop-evidence');

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $filename = sprintf(
                'ws%d-p%d-w%d-%d.%s',
                $workshop->id,
                $participant->id,
                $validated['week_no'],
                time(),
                strtolower($file->getClientOriginalExtension() ?: 'dat')
            );

            $file->move($directory, $filename);
            $attributes['file_path'] = 'uploads/workshop-evidence/' . $filename;
        }

        WorkshopEvidence::updateOrCreate(
            [
                'participant_id' => $participant->id,
                'week_no' => $validated['week_no'],
            ],
            $attributes
        );

        return back()->with('success', "Bukti kehadiran minggu ke-{$validated['week_no']} berhasil dikirim.");
    }

    /**
     * Admin / OMDD approve or reject a submitted weekly evidence.
     */
    public function review(Request $request, Workshop $workshop, WorkshopEvidence $evidence): RedirectResponse
    {
        abort_unless((int) $evidence->participant?->workshop_id === (int) $workshop->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $evidence->update([
            'status' => $validated['status'],
            'review_note' => $validated['review_note'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Status bukti kehadiran berhasil diperbarui.');
    }
}
