<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Send a message from the authenticated user to another user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        try {
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message
            ]);

            Log::info('Message sent', ['message_id' => $message->id]);

            return response()->json([
                'success' => true,
                'data' => $message,
                'message' => 'Message sent successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error sending message', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending message'
            ], 500);
        }
    }

    /**
     * Retrieve all messages for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages()
    {
        try {
            $messages = Message::where('receiver_id', Auth::id())
                ->orWhere('sender_id', Auth::id())
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Fetched messages', ['user_id' => Auth::id()]);

            return response()->json([
                'success' => true,
                'data' => $messages,
                'message' => 'Messages fetched successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching messages', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching messages'
            ], 500);
        }
    }
}
