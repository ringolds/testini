<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TestController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('admin')->group(function(){
    Route::resource('map', MapController::class); 
});

Route::middleware('auth')->group(function() {
    Route::get('map/{map}/config', [MapController::class, 'getConfig'])->name('map.config'); 
    Route::resource('test', TestController::class); 
    Route::resource('bank', BankController::class); 
    Route::resource('question', QuestionController::class);
    Route::post('/logout', [AuthController::class, 'logout']) ->name('logout');  
    Route::get('/bank/{bank}/questions', [BankController::class, 'addQuestion']) ->name('addQuestion');
    Route::get('/test/{test}/questions', [TestController::class, 'addQuestion']) ->name('addQuestionToTest');   
    Route::post('/question/{question}/bank/{bank}', [QuestionController::class, 'addToBank'])->name('question.addToBank');
    Route::delete('/question/{question}/bank/{bank}', [QuestionController::class, 'removeFromBank'])->name('question.removeFromBank');
    Route::post('/question/{question}/test/{test}', [QuestionController::class, 'addToTest'])->name('question.addToTest');
    Route::delete('/question/{question}/test/{test}', [QuestionController::class, 'removeFromTest'])->name('question.removeFromTest');
});

Route::middleware('guest')->group(function() {
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);    
});