<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageApiController extends BaseApiController
{
    /**
     * Display a listing of messages for a user.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('user_id');

        $query = Message::with(['sender', 'receiver']);

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            });
        }

        if ($request->boolean('unread_only')) {
            $query->where('is_read', false);
        }

        $messages = $query->latest()->paginate($request->integer('per_page', 20));

        return $this->sendResponse($messages, 'Messages retrieved successfully');
    }

    /**
     * Send a new message.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:user_accounts,id',
            'receiver_id' => 'required|exists:user_accounts,id',
            'message' => 'required|string',
            'related_type' => 'nullable|string|max:100',
            'related_id' => 'nullable|integer',
            'attachment_path' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray(), 422);
        }

        $message = Message::create($request->all());

        return $this->sendResponse($message->load(['sender', 'receiver']), 'Message sent successfully', 201);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(int $id): JsonResponse
    {
        $message = Message::find($id);

        if (!$message) {
            return $this->sendError('Message not found', [], 404);
        }

        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this->sendResponse($message, 'Message marked as read');
    }
}

