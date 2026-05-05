<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('auth')->group(function() {
    Route::resource('bank', BankController::class); 
    Route::post('/logout', [AuthController::class, 'logout']) ->name('logout');    
});

Route::middleware('guest')->group(function() {
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);    
});