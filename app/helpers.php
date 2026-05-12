<?php
use App\Models\Notification;

if (!function_exists('sendNotification')) {
    function sendNotification($userId, $title, $message)
    {
        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
        ]);
    }
}
