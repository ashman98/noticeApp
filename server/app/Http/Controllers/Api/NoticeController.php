<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationSent;
use Illuminate\Http\Request;

class NoticeController
{
    public function sendWebSocketNotification(Request $request)
    {
        $request->validate([
//            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
//            'body' => 'required|string',
        ]);

        // Fire the broadcast event
        event(new NotificationSent($request->message));

        return response()->json(['message' => 'Notification sent successfully']);
    }

}
