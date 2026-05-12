<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
        public function index()
    {
        return PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update($request->all());

        return response()->json([
            'message' => 'updated'
        ]);
    }
}
