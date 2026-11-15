<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use App\Models\UserAccount;
use App\Models\WorkshopScoreReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkshopScoreReportApiController extends BaseApiController
{
    /**
     * Stage 5 (Admin): List generated workshop score reports.
     */
    public function index(Request $request): JsonResponse
    {
        $reports = WorkshopScoreReport::with(['workshop', 'generator'])
            ->latest('generated_at')
            ->paginate($request->integer('per_page', 10));

        return $this->sendResponse($reports, 'Score reports retrieved successfully');
    }

    /**
     * Stage 5 (Admin): Generate a score report record for a workshop.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'workshop_id' => 'required|exists:workshops,id',
            'generated_by' => 'required|exists:user_accounts,id',
            'pdf_path' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $report = WorkshopScoreReport::create([
            'workshop_id' => $request->input('workshop_id'),
            'generated_by' => $request->input('generated_by'),
            'pdf_path' => $request->input('pdf_path'),
            'generated_at' => now(),
            'status' => 'generated',
        ]);

        return $this->sendResponse($report->load(['workshop', 'generator']), 'Score report generated successfully', 201);
    }

    /**
     * Stage 5 (Admin): Distribute report to HRD and/or Director (sends message notification).
     */
    public function distribute(Request $request, int $id): JsonResponse
    {
        $report = WorkshopScoreReport::with('workshop')->find($id);

        if (!$report) {
            return $this->sendError('Score report not found', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'recipient_role' => 'required|in:hrd,director,both',
            'sender_id' => 'required|exists:user_accounts,id',
            'custom_message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $role = $request->input('recipient_role');
        $senderId = $request->input('sender_id');
        $note = $request->input('custom_message', "Laporan nilai Workshop TPS-G {$report->workshop->title} telah selesai.");

        // Send to HRD
        if ($role === 'hrd' || $role === 'both') {
            $hrdUsers = UserAccount::where('role', 'hrd')->get();
            foreach ($hrdUsers as $hrd) {
                Message::create([
                    'sender_id' => $senderId,
                    'receiver_id' => $hrd->id,
                    'related_type' => 'workshop_score_report',
                    'related_id' => $report->id,
                    'message' => $note,
                    'attachment_path' => $report->pdf_path,
                ]);
            }
            $report->sent_to_hrd_at = now();
            $report->status = 'sent_hrd';
        }

        // Send to Director
        if ($role === 'director' || $role === 'both') {
            $directorUsers = UserAccount::where('role', 'director')->get();
            foreach ($directorUsers as $dir) {
                Message::create([
                    'sender_id' => $senderId,
                    'receiver_id' => $dir->id,
                    'related_type' => 'workshop_score_report',
                    'related_id' => $report->id,
                    'message' => $note,
                    'attachment_path' => $report->pdf_path,
                ]);
            }
            $report->sent_to_director_at = now();
            $report->status = $role === 'both' ? 'completed' : 'sent_director';
        }

        $report->save();

        return $this->sendResponse($report, 'Score report distributed successfully');
    }
}

