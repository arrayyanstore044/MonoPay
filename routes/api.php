<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\AuthController;
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

use App\Http\Controllers\User\UserController;

Route::middleware(['auth.session'])->group(function () {
    Route::get('/users/current', [UserController::class, 'getCurrentUser']);
});

// Rute verifikasi email dummy agar tidak error saat memanggil sendEmailVerificationNotification
Route::get('/email/verify/{id}/{hash}', function () {
    return response()->json(['message' => 'Email verified.']);
})->middleware(['signed'])->name('verification.verify');
