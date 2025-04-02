<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GoogleAuthController;
use App\Http\Controllers\API\HotelController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\TransactionController;
use Illuminate\Support\Facades\Route;







Route::apiResource('orders', OrderController::class);
Route::post('/payments/vnpay', [PaymentController::class, 'createVnpayUrl']);
Route::get('/payments/vnpay/callback', [PaymentController::class, 'vnpayCallback']);
Route::get('/transactions', [TransactionController::class, 'index']);
Route::get('/transactions/{id}', [TransactionController::class, 'show']);





// momo
Route::post('/payments/momo', [PaymentController::class, 'createMomoUrl']);
Route::get('/payments/momo/callback', [PaymentController::class, 'momoCallback']);
Route::post('/payments/momo/notify', [PaymentController::class, 'momoNotify']);



Route::post('/auth/import', [AuthController::class, 'importExcel']);
Route::post('/auth/import-hotels', [AuthController::class, 'importHotels']);
// auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/auth/verify', [AuthController::class, 'verifyEmail']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);


// hotels
Route::get('/hotels', [HotelController::class, 'index']);
Route::middleware(['auth:api', 'role:admin'])->get('/admin', function () {
    return response()->json(['message' => 'Chào mừng Admin']);
});
Route::get('/test-email', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('This is a test email from Laravel.', function ($message) {
            $message->to('ht01252004@gmail.com')
                ->subject('Test Email');
        });
        \Illuminate\Support\Facades\Log::info('Test email sent successfully');
        return 'Email sent!';
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Test email failed: ' . $e->getMessage());
        return 'Email failed: ' . $e->getMessage();
    }
});
