<?php

use App\Http\Controllers\SnippeWebhookController;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('webhooks/notefymevia', [SnippeWebhookController::class, 'handle'])->name('webhooks.notefymevia');

