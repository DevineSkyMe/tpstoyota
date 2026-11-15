<?php

namespace App\Http\Controllers;

use App\Models\ActivityPlan;
use App\Models\ActivityProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityProgressController extends Controller
{
    /**
     * The owner of an activity (Karyawan) submits a progress update.
     * Admins may also post an update on behalf of the team.
     */
    public function store(Request $request, int $id): RedirectResponse
    {
        $activity = ActivityPlan::findOrFail($id);
        $user = $request->user();

        if ($user->isKaryawan() && (int) $activity->employee_id !== (int) $user->employee_id) {
            abort(403, 'Anda hanya dapat mengirim perkembangan untuk activity milik sendiri.');
        }

        $validated = $request->validate([
            'status_update' => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
            'progress_pct' => 'nullable|integer|min:0|max:100',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $attributes = [
            'activity_plan_id' => $activity->id,
            'submitted_by' => $user->id,
            'status_update' => $validated['status_update'],
            'description' => $validated['description'] ?? null,
            'progress_pct' => $validated['progress_pct'] ?? null,
            'approval_status' => ActivityProgress::STATUS_PENDING,
            'admin_note' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $directory = public_path('uploads/activity-progress');

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $filename = sprintf(
                'act%d-%d.%s',
                $activity->id,
                time(),
                strtolower($file->getClientOriginalExtension() ?: 'jpg')
            );

            $file->move($directory, $filename);
            $attributes['image_path'] = 'uploads/activity-progress/' . $filename;
        }

        ActivityProgress::create($attributes);

        return back()->with('success', 'Perkembangan activity berhasil dikirim dan menunggu persetujuan Admin.');
    }

    /**
     * Admin approves (ACC) or rejects a submitted progress update and may
     * leave a feedback note for the owner.
     */
    public function review(Request $request, int $id, int $progressId): RedirectResponse
    {
        $activity = ActivityPlan::findOrFail($id);

        $progress = ActivityProgress::where('activity_plan_id', $activity->id)->findOrFail($progressId);

        $validated = $request->validate([
            'approval_status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $progress->update([
            'approval_status' => $validated['approval_status'],
            'admin_note' => $validated['admin_note'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        // An approved update becomes the activity's official progress figure.
        if ($validated['approval_status'] === ActivityProgress::STATUS_APPROVED && $progress->progress_pct !== null) {
            $pct = (int) $progress->progress_pct;

            $activity->update([
                'progress_pct' => $pct,
                'status' => $pct >= 100
                    ? 'completed'
                    : ($activity->status === 'not_started' ? 'in_progress' : $activity->status),
                'actual_end' => $pct >= 100 ? ($activity->actual_end ?? now()->toDateString()) : $activity->actual_end,
            ]);
        }

        $label = $validated['approval_status'] === ActivityProgress::STATUS_APPROVED ? 'disetujui' : 'ditolak';

        return back()->with('success', "Perkembangan activity berhasil {$label}.");
    }
}
