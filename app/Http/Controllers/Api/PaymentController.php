<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $request, PaystackService $paystack){
    $order = Order::findOrFail($request->order_id);

    // 🔥 أنشئ reference خاص بالدفع
    $reference = 'PAY-' . uniqid();

    if ($request->payment_method === 'paystack') {
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
            
        return response()->json([
            'status' => true,
            'authorization_url' => $payment['data']['authorization_url'],
            'reference' => $reference
        ]);
    }
     // 🟡 الدفع عند الاستلام
    if ($request->payment_method === 'cash') {
        $order->update(['status' => 'pending_payment']);
        return response()->json([
            'status' => true,
            'message' => 'تم تأكيد الطلب وسيتم الدفع عند الاستلام'
        ]);
    }
        // ❌ طرق غير مفعلة
    return response()->json([
        'status' => false,
        'message' => 'طريقة الدفع غير مفعلة حالياً'
    ], 400);
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
public function webhook(Request $request)
{
    // 1) التحقق من أن الطلب قادم من Paystack
    $signature = $request->header('x-paystack-signature');

    if (!$signature) {
        return response()->json(['message' => 'Invalid signature'], 400);
    }

    // 2) تأكيد صحة التوقيع
    $computed = hash_hmac('sha512', $request->getContent(), env('PAYSTACK_SECRET_KEY'));

    if ($signature !== $computed) {
        return response()->json(['message' => 'Signature mismatch'], 400);
    }

    // 3) قراءة البيانات
    $event = $request->event;
    $data = $request->data;

    // 4) نتحقق من نوع الحدث
    if ($event === 'charge.success') {

        $reference = $data['reference'];

        // 5) إيجاد الدفع
        $payment = Payment::where('reference', $reference)->first();

        if ($payment && $payment->status !== 'paid') {

            // تحديث الدفع
            $payment->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);

            // تحديث الطلب
            $payment->order->update([
                'status' => 'processing',
                'paid_at' => now()
            ]);
        }
    }

    return response()->json(['message' => 'Webhook received']);
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