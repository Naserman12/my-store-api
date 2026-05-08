<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $request, PaystackService $paystack)
{
    $order = Order::findOrFail($request->order_id);

    // 🔥 أنشئ reference خاص بالدفع
    $reference = 'PAY-' . uniqid();

    // خزّنه في جدول الدفع
    $order->payment()->update([
        'amount' => $order->total,
        'status' => 'pending',
        'reference' => $reference,
        'payment_method' => $request->payment_method,
    ]);

    // Paystack يحتاج amount * 100
    $payment = $paystack->initializePayment(
        $order->customer_email,
        $order->total * 100,
        $reference
    );

    return response()->json($payment);
}
public function verify($reference, PaystackService $paystack)
{
    $response = $paystack->verifyPayment($reference);

    if (
        isset($response['data']['status']) &&
        $response['data']['status'] === 'success'
    ) {

        // 🔥 ابحث عن الدفع وليس الطلب
        $payment = Payment::where('reference', $reference)->firstOrFail();
        $order = $payment->order;

        // حدّث الدفع
        $payment->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        // حدّث الطلب
        $order->update([
            'status' => 'processing',
            'paid_at' => now()
        ]);
    }

    return response()->json($response);
}


    // public function pay(Request $request, PaystackService $paystack)
    // {
    //     $order = Order::findOrFail($request->order_id);
        
    //     $payment = $paystack->initializePayment(
    //         $order->customer_email,
    //         $order->total,
    //         $order->order_number
    //     );
    //     return response()->json($payment);
    // }

    // public function verify($reference, PaystackService $paystack)
    // {
    //     $response = $paystack->verifyPayment($reference);

    //     if (
    //         isset($response['data']['status']) &&
    //         $response['data']['status'] === 'success'
    //     ) {

    //         $order = Order::where(
    //             'order_number',
    //             $reference
    //         )->first();

    //         if ($order) {

    //             $order->update([
    //                 'status' => 'processing',
    //                 'paid_at' => now()
    //             ]);
    //         }
    //     }

    //     return response()->json($response);
    // }
}