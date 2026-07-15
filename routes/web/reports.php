<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('reports')->group(function () {
    Route::get('collection-report', [ReportController::class, 'collection'])->name('reports.collection');
    Route::get('payments-report', [ReportController::class, 'payments'])->name('reports.payments');
    Route::get('crops-report', [ReportController::class, 'crops'])->name('reports.crops');
    Route::get('collection-report', [ReportController::class, 'collection'])->name('reports.collection');
});
