<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HotelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/import', [AuthController::class, 'showImportForm']);
Route::get('/hotels', [HotelController::class, 'index']);
