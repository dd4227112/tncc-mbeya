<?php

use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('members/getMembers', [UserManagementController::class, 'getUsers'])->name('members.getUsers');
    Route::resource('members', UserManagementController::class);

    // Route::get('/getUnits', [UserManagementController::class, 'getUnits'])->name('members.getUnits');

    // Route::get('/', [UserManagementController::class, 'index'])->name('members.index');
    // Route::get('/create', [UserManagementController::class, 'create'])->name('members.create');
    // Route::post('/', [UserManagementController::class, 'store'])->name('members.store');
    // Route::get('/{member}', [UserManagementController::class, 'show'])->name('members.show');
    // Route::get('/{member}/edit', [UserManagementController::class, 'edit'])->name('members.edit');
    // Route::put('/{member}', [UserManagementController::class, 'update'])->name('members.update');
    // Route::delete('/{member}', [UserManagementController::class, 'destroy'])->name('members.destroy');
});
