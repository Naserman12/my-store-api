<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaystackService
{
    public function initializePayment($email, $amount, $reference)
    {
        $response = Http::withoutVerifying()
        ->withToken(
            config('services.paystack.secret_key')
        )->post(
            config('services.paystack.payment_url') . '/transaction/initialize',
            [
                'email' => $email,
                'amount' => $amount * 100, // paystack يعتمد kobo
                'reference' => $reference,
                'currency' => 'NGN',
                'verify' => false,
                'callback_url' =>
                'https://my-store-2f36e.web.app/payment-success'
            ]
        );

        return $response->json();
    }

    public function verifyPayment($reference)
    {
        $response = Http::withToken(
            config('services.paystack.secret_key')
        )->get(
            config('services.paystack.payment_url') .
            '/transaction/verify/' .
            $reference
        );

        return $response->json();
    }
}