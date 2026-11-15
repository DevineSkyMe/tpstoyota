<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\UserAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Show the in-app inbox / outbox of the authenticated account.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $box = $request->input('box', 'inbox') === 'sent' ? 'sent' : 'inbox';

        $messages = Message::with(['sender', 'receiver'])
            ->when($box === 'inbox', fn ($q) => $q->where('receiver_id', $user->id))
            ->when($box === 'sent', fn ($q) => $q->where('sender_id', $user->id))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $unread = Message::where('receiver_id', $user->id)->where('is_read', false)->count();

        $recipients = $user->hasRole(UserAccount::ROLE_ADMIN, UserAccount::ROLE_HRD, UserAccount::ROLE_OMDD)
            ? UserAccount::with('employee')->where('id', '!=', $user->id)->orderBy('role')->get()
            : collect();

        return view('messages.index', compact('messages', 'box', 'unread', 'recipients'));
    }

    /**
     * Open one message and mark it as read for the receiver.
     */
    public function show(Request $request, Message $message): View
    {
        abort_unless(
            (int) $message->receiver_id === (int) $request->user()->id
            || (int) $message->sender_id === (int) $request->user()->id
            || $request->user()->isAdmin(),
            403
        );

        if ((int) $message->receiver_id === (int) $request->user()->id && ! $message->is_read) {
            $message->update(['is_read' => true, 'read_at' => now()]);
        }

        $message->load(['sender', 'receiver']);

        return view('messages.show', compact('message'));
    }

    /**
     * Compose an internal message / report notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:user_accounts,id'],
            'message' => ['required', 'string', 'max:2000'],
            'attachment_path' => ['nullable', 'string', 'max:255'],
        ]);

        Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $validated['receiver_id'],
            'related_type' => 'manual_message',
            'message' => $validated['message'],
            'attachment_path' => $validated['attachment_path'] ?? null,
            'is_read' => false,
        ]);

        return redirect()->route('messages.index', ['box' => 'sent'])
            ->with('success', 'Pesan berhasil dikirim.');
    }
}
