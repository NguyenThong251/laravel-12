<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('payment')->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $order = Order::create([
            'order_code' => 'ORD' . time(),
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = Order::with('payment')->findOrFail($id);
        return response()->json($order);
    }
}
