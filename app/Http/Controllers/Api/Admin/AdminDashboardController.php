<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $week = Carbon::now()->startOfWeek();
        $month = Carbon::now()->startOfMonth();

        return response()->json([
            'total_sales' => Order::sum('total'),

            'today_sales' =>
                Order::whereDate('created_at', $today)
                ->sum('total'),

            'weekly_sales' =>
                Order::where('created_at', '>=', $week)
                ->sum('total'),

            'monthly_sales' =>
                Order::where('created_at', '>=', $month)
                ->sum('total'),
        ]);
    }
}
    

