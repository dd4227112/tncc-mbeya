<?php

use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('settings')->group(function () {

    Route::get('/', [SettingController::class, 'roles'])->name('settings.roles');
    Route::post('/roles/toggle-permission', [SettingController::class, 'togglePermission'])
        ->name('roles.toggle-permission');
    Route::post('/create', [SettingController::class, 'create'])->name('roles.store');
    Route::get('/getRole/{id}', [SettingController::class, 'getUserRole'])->name('settings.getRole');
    Route::post('/updateUserRole/{id}', [SettingController::class, 'updateUserRole'])->name('settings.updateUserRole');

});
