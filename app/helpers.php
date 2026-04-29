<?php
use App\Models\Notification;

function sendNotification($userId, $title, $message)
{
    Notification::create([
        'user_id' => $userId,
        'title' => $title,
        'message' => $message
    ]);
}