<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    //
    // checkout my orders
    public function myOrders(Request $request)
{
    return $request->user()
        ->orders()
        ->with('items', 'statusLogs')
        ->latest()
        ->get();
}

public function invoice($id)
{
    $order = Order::with('items')->findOrFail($id);

    $pdf = Pdf::loadView('order-invoice', compact('order'));

    return view('order-invoice', compact('order'));
}
public function show(Request $request, $id)
{
    $order = Order::with([
        'items.product.images',
        'statusLogs'
    ])->findOrFail($id);


    $user = $request->user();

    // 👤 لو فيه user → تحقق الملكية
    if ($user && $order->user_id !== $user->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return $order;
}
}
