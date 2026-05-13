<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class AdminPaymentMethodController extends Controller
{
    public function index()
    {
        return PaymentMethod::latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:payment_methods,code',
            'public_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'logo' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $method = PaymentMethod::create([
            'name' => $data['name'],
            'code' => $data['code'],
             'config' => [
                 'public_key' => $data['public_key'] ?? null,
                 'secret_key' => $data['secret_key'] ?? null,
                 'currency' => $data['currency'] ?? null,
             ],
            'logo' => $data['logo'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Payment method created ✅',
            'data' => $method
        ], 201);
    }
}