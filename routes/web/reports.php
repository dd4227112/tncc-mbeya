<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('reports')->group(function () {
    Route::get('collection-report', [ReportController::class, 'collection'])->name('reports.collection');
    Route::get('get-collection-report', [ReportController::class, 'getCollection'])->name('reports.getCollection');

    Route::get('crop_performance-report', [ReportController::class, 'crops'])->name('reports.crop_performance-report');
    Route::get('get-crop_performance-report', [ReportController::class, 'getCropsReportData'])->name('reports.get-crop_performance-report');
    Route::get('collection-report', [ReportController::class, 'collection'])->name('reports.collection');
});
