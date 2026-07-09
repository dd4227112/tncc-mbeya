<?php

use App\Http\Controllers\CropsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('crops')->group(function () {
    Route::get('/getCrops', [CropsController::class, 'getCrops'])->name('crops.getCrops');
    Route::get('/searchCrop/{term}', [CropsController::class, 'searchCrop'])->name('crops.searchCrop');

    
    Route::get('/', [CropsController::class, 'index'])->name('crops.index');
    Route::get('/create', [CropsController::class, 'create'])->name('crops.create');
    Route::post('/', [CropsController::class, 'store'])->name('crops.store');
    Route::get('/{crop}', [CropsController::class, 'show'])->name('crops.show');
    Route::get('/{crop}/edit', [CropsController::class, 'edit'])->name('crops.edit');
    Route::put('/{crop}', [CropsController::class, 'update'])->name('crops.update');
    Route::delete('/{crop}', [CropsController::class, 'destroy'])->name('crops.destroy');
});
