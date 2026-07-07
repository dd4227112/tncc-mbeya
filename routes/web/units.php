<?php

use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('units')->group(function () {
    Route::get('/getUnits', [UnitController::class, 'getUnits'])->name('units.getUnits');
    
    Route::get('/', [UnitController::class, 'index'])->name('units.index');
    Route::get('/create', [UnitController::class, 'create'])->name('units.create');
    Route::post('/', [UnitController::class, 'store'])->name('units.store');
    Route::get('/{unit}', [UnitController::class, 'show'])->name('units.show');
    Route::get('/{unit}/edit', [UnitController::class, 'edit'])->name('units.edit');
    Route::put('/{unit}', [UnitController::class, 'update'])->name('units.update');
    Route::delete('/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');
});
