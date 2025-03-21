<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Services\OnlinePaymentService;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('payment')->group(function () {
    Route::get('/confirm/{order_number}', [PaymentController::class, 'confirm'])->name('payment.confirm');
    Route::get('/failed/{order_number}', [PaymentController::class, 'failed'])->name('payment.failed');
});

Route::get('/private-files/{path}', function ($path) {
    $filePath = "private/{$path}";

    if (Storage::disk('private')->exists($filePath)) {
        return response()->file(Storage::disk('private')->path($filePath));
    }

    abort(404);
})->where('path', '.*');
