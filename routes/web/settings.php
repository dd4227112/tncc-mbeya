<?php

use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('settings')->group(function () {

    Route::get('/', [SettingController::class, 'roles'])->name('settings.roles');
    Route::post('/roles/toggle-permission', [SettingController::class, 'togglePermission'])->name('roles.toggle-permission');
    Route::get('/trash', [SettingController::class, 'trash'])->name('settings.trash');
    Route::post('/trash/getTrashData', [SettingController::class, 'getTrashData'])->name('settings.getTrashData');
    Route::post('/create', [SettingController::class, 'create'])->name('roles.store');
    Route::get('/getRole/{id}', [SettingController::class, 'getUserRole'])->name('settings.getRole');
    Route::post('/updateUserRole/{id}', [SettingController::class, 'updateUserRole'])->name('settings.updateUserRole');
    Route::post('/trash/restore', [SettingController::class, 'restoreTrash'])->name('settings.restoreItem');
    Route::post('/trash/delete', [SettingController::class, 'deleteTrash'])->name('settings.deleteItem');});
