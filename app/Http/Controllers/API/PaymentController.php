<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createVnpayUrl(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:1000',
            'bank_code' => 'nullable|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Thông tin thanh toán VNPay
        $vnp_TxnRef = $order->order_code . '_' . time(); // Mã giao dịch duy nhất
        $vnp_OrderInfo = 'Thanh toan don hang ' . $order->order_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->amount * 100; // VNPay tính theo cent (1 VND = 100)
        $vnp_Locale = 'vn';
        $vnp_BankCode = $request->bank_code ?? '';
        $vnp_IpAddr = $request->ip();
        $vnp_CreateDate = date('YmdHis');
        $vnp_ReturnUrl = config('vnpay.return_url');

        // Tạo dữ liệu gửi tới VNPay
        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => config('vnpay.tmn_code'),
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $vnp_CreateDate,
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_ReturnUrl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        if ($vnp_BankCode) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        // Tạo secure hash
        ksort($inputData);
        $query = http_build_query($inputData);
        $vnp_SecureHash = hash_hmac('sha512', $query, config('vnpay.hash_secret'));
        $vnpayUrl = config('vnpay.url') . '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

        // Lưu thông tin thanh toán
        Payment::create([
            'order_id' => $order->id,
            'transaction_id' => $vnp_TxnRef,
            'status' => 'pending',
        ]);

        return response()->json(['url' => $vnpayUrl]);
    }

    public function vnpayCallback(Request $request)
    {
        $vnp_HashSecret = config('vnpay.hash_secret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']); // Xóa secure hash để kiểm tra

        // Tạo chuỗi hash để so sánh
        ksort($inputData);
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Lấy thông tin giao dịch
        $txnRef = $inputData['vnp_TxnRef'];
        $orderCode = explode('_', $txnRef)[0];
        $order = Order::where('order_code', $orderCode)->firstOrFail();
        $payment = Payment::where('transaction_id', $txnRef)->firstOrFail();

        // Kiểm tra tính hợp lệ của giao dịch
        if ($secureHash === $vnp_SecureHash && $inputData['vnp_ResponseCode'] == '00') {
            $order->update(['status' => 'paid']);
            $payment->update(['status' => 'success']);
            return response()->json([
                'message' => 'Thanh toán thành công',
                'data' => $inputData,
            ]);
        } else {
            $order->update(['status' => 'failed']);
            $payment->update(['status' => 'failed']);
            return response()->json([
                'message' => 'Thanh toán thất bại',
                'data' => $inputData,
            ], 400);
        }
    }




    // momo


    public function createMomoUrl(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:1000',
        ]);

        $order = Order::findOrFail($request->order_id);

        $data = [
            'partnerCode' => config('momo.partner_code'),
            'partnerName' => 'Test Payment', // Tên đối tác (tùy chọn)
            'storeId' => 'MoMoTestStore', // ID cửa hàng (tùy chọn)
            'requestId' => $order->order_code . '_' . time(), // Mã yêu cầu duy nhất
            'amount' => (int)$request->amount, // Số tiền (VND)
            'orderId' => $order->order_code,
            'orderInfo' => 'Thanh toan don hang ' . $order->order_code,
            'redirectUrl' => config('momo.return_url'),
            'ipnUrl' => config('momo.notify_url'),
            'requestType' => 'captureWallet', // Loại yêu cầu
            'extraData' => '', // Dữ liệu bổ sung (base64 encoded nếu cần)
            'lang' => 'vi',
        ];

        // Tạo chữ ký (signature)
        $rawHash = "accessKey=" . config('momo.access_key') .
            "&amount=" . $data['amount'] .
            "&extraData=" . $data['extraData'] .
            "&ipnUrl=" . $data['ipnUrl'] .
            "&orderId=" . $data['orderId'] .
            "&orderInfo=" . $data['orderInfo'] .
            "&partnerCode=" . $data['partnerCode'] .
            "&redirectUrl=" . $data['redirectUrl'] .
            "&requestId=" . $data['requestId'] .
            "&requestType=" . $data['requestType'];
        $data['signature'] = hash_hmac('sha256', $rawHash, config('momo.secret_key'));

        // Gọi API MoMo
        $response = $this->callMomoApi($data);

        if ($response['resultCode'] == 0) {
            // Lưu thông tin thanh toán
            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $data['requestId'],
                'payment_method' => 'momo',
                'status' => 'pending',
            ]);

            return response()->json(['url' => $response['payUrl']]);
        } else {
            return response()->json(['error' => $response['message']], 400);
        }
    }

    public function momoCallback(Request $request)
    {
        Log::info('MoMo Callback', $request->all());

        $data = $request->all();
        $orderId = $data['orderId'];
        $requestId = $data['requestId'];

        $order = Order::where('order_code', $orderId)->firstOrFail();
        $payment = Payment::where('transaction_id', $requestId)->firstOrFail();

        // Kiểm tra chữ ký
        $rawHash = "accessKey=" . config('momo.access_key') .
            "&amount=" . $data['amount'] .
            "&extraData=" . $data['extraData'] .
            "&message=" . $data['message'] .
            "&orderId=" . $data['orderId'] .
            "&orderInfo=" . $data['orderInfo'] .
            "&partnerCode=" . $data['partnerCode'] .
            "&requestId=" . $data['requestId'] .
            "&responseTime=" . $data['responseTime'] .
            "&resultCode=" . $data['resultCode'];
        $signature = hash_hmac('sha256', $rawHash, config('momo.secret_key'));

        if ($signature === $data['signature'] && $data['resultCode'] == 0) {
            $order->update(['status' => 'paid']);
            $payment->update(['status' => 'success']);
            return response()->json(['message' => 'Thanh toán thành công']);
        } else {
            $order->update(['status' => 'failed']);
            $payment->update(['status' => 'failed']);
            return response()->json(['message' => 'Thanh toán thất bại'], 400);
        }
    }

    public function momoNotify(Request $request)
    {
        // Xử lý thông báo IPN từ MoMo (tương tự callback nhưng không trả response cho người dùng)
        Log::info('MoMo Notify', $request->all());
        return response()->json(['status' => 'received']);
    }

    private function callMomoApi($data)
    {
        $ch = curl_init(config('momo.url'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
