<?php

use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('members/getMembers', [UserManagementController::class, 'getUsers'])->name('members.getUsers');
    Route::get('members/searchMember/{query}', [UserManagementController::class, 'searchMember'])->name('members.searchMember');

    Route::resource('members', UserManagementController::class);
});
