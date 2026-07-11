<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('payments/getpayments', [PaymentController::class, 'getpayments'])->name('payments.getpayments');
    Route::post('payments/addPayment', [PaymentController::class, 'addPayment'])->name('payments.addPayment');
    Route::get('payments/{payment}/details', [PaymentController::class, 'details'])->name('payments.details');
    Route::resource('payments', PaymentController::class);
});
