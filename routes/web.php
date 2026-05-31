<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\QuestionController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('auth')->group(function() {
    Route::resource('map', MapController::class); 
    Route::resource('bank', BankController::class); 
    Route::resource('question', QuestionController::class);
    Route::post('/logout', [AuthController::class, 'logout']) ->name('logout');  
    Route::get('/bank/{bank}/questions', [BankController::class, 'addQuestion']) ->name('addQuestion');  
    Route::post('/question/{question}/bank/{bank}', [QuestionController::class, 'addToBank'])->name('question.addToBank');
    Route::delete('/question/{question}/bank/{bank}', [QuestionController::class, 'removeFromBank'])->name('question.removeFromBank');
});

Route::middleware('guest')->group(function() {
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);    
});