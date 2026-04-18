<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    //
    // get all orders for admin dashboard
    public function index()
{
    $orders = Order::with([
        'user:id,name,email',
        'items.product:id,name'
    ])
    ->latest()
    ->paginate(10);

    return response()->json($orders);
}
// update order status
public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
    ]);

    $order->update([
        'status' => $request->status
    ]);

    return response()->json([
        'message' => 'Order updated successfully'
    ]);
}
// get latest 5 orders for admin dashboard
    public function latest()
{
    $orders = Order::latest()
        ->take(5)
        ->get([
            'id',
            'status',
            'total',
            'created_at'
        ]);

    return response()->json($orders);
}
// get order details for admin
public function show(Order $order)
{
    $order->load([
        'user:id,name,email',
        'items.product:id,name,price,image'
    ]);

    return response()->json($order);
}
}
