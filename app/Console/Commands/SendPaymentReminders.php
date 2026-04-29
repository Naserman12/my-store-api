<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Order;

#[Signature('app:send-payment-reminders')]
#[Description('Command description')]
class SendPaymentReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        
            $orders = Order::where('status', 'pending')
        ->where('created_at', '<=', now()->subHours(2))
        ->get();

    foreach ($orders as $order) {

        sendNotification(
            $order->user_id,
            "⏰ تذكير بالدفع",
            "لم تقم بدفع طلبك رقم {$order->order_number}"
        );
    }
    }
}
