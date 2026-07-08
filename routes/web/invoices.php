<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('invoices/getInvoices', [InvoiceController::class, 'getInvoices'])->name('invoices.getInvoices');
    Route::resource('invoices', InvoiceController::class);
});
