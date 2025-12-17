<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * عرض كل الرسائل المتعلقة باليوزر الحالي (مرسلة + مستلمة)
     */
    public function index()
    {
        $userId = Auth::id(); // ده بيرجع user_id

        $messages = Chat::with(['sender', 'receiver'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('sent_at', 'asc')
            ->get();

        return response()->json([
            'message' => 'User messages fetched successfully',
            'data' => $messages,
        ]);
    }

    /**
     * إرسال رسالة جديدة
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        $data = $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'content'     => 'required|string',
        ]);

        $data['sender_id'] = $userId;
        $data['sent_at'] = now();
        $data['is_read'] = false;

        $message = Chat::create($data);

        return response()->json([
            'message' => 'Message sent successfully',
            'data'    => $message,
        ], 201);
    }

    /**
     * عرض المحادثة بين اليوزر الحالي ويوزر تاني (doctor/parent)
     */
    public function conversation($otherUserId)
    {
        $userId = Auth::id();

        // تأكدي إن اليوزر التاني موجود
        $otherUser = User::where('user_id', $otherUserId)->firstOrFail();

        $messages = Chat::with(['sender', 'receiver'])
            ->where(function ($q) use ($userId, $otherUserId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $otherUserId);
            })
            ->orWhere(function ($q) use ($userId, $otherUserId) {
                $q->where('sender_id', $otherUserId)
                  ->where('receiver_id', $userId);
            })
            ->orderBy('sent_at', 'asc')
            ->get();

        // نعلّم رسائل اليوزر التاني كـ مقروءة
        Chat::where('sender_id', $otherUserId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'Conversation fetched successfully',
            'other_user' => $otherUser,
            'data' => $messages,
        ]);
    }

    /**
     * تعليم رسالة واحدة كمقروءة
     */
    public function markAsRead($messageId)
    {
        $userId = Auth::id();

        $message = Chat::where('message_id', $messageId)
            ->where('receiver_id', $userId)
            ->firstOrFail();

        $message->update(['is_read' => true]);

        return response()->json([
            'message' => 'Message marked as read',
            'data' => $message,
        ]);
    }
}
