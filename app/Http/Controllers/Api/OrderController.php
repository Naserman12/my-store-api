<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function myOrders(Request $request)
{
    return $request->user()
        ->orders()
        ->with('items')
        ->latest()
        ->get();
}
public function show(Request $request, $id)
{
    return $request->user()
        ->orders()
        ->with('items')
        ->findOrFail($id);
}
}
