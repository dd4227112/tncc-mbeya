<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', function () {
    return view('login');
})->name('login');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/web/units.php';
require __DIR__ . '/web/crops.php';
require __DIR__ . '/web/staffs.php';
require __DIR__ . '/web/members.php';
require __DIR__ . '/web/invoices.php';
require __DIR__ . '/web/payments.php';
require __DIR__ . '/web/dashboard.php';
require __DIR__ . '/web/reports.php';
require __DIR__ . '/web/settings.php';
