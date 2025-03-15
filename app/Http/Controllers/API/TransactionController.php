<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tất cả giao dịch với thông tin đơn hàng
        $transactions = Payment::with('order')->latest()->get();

        // Tùy chọn: Thêm phân trang nếu cần
        // $transactions = Payment::with('order')->latest()->paginate(10);

        return response()->json([
            'message' => 'Lịch sử giao dịch',
            'data' => $transactions,
        ]);
    }

    public function show($id)
    {
        // Lấy chi tiết một giao dịch theo ID
        $transaction = Payment::with('order')->findOrFail($id);

        return response()->json([
            'message' => 'Chi tiết giao dịch',
            'data' => $transaction,
        ]);
    }
}
