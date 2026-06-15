<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Session;


Route::middleware('admin')->group(function(){
    Route::resource('map', MapController::class); 
});

Route::get('lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'lv'])) {
        Session::put('locale', $locale);
    }

    return redirect()->back();
})->name('lang.switch');

Route::middleware('auth')->group(function() {
    //rating
    route::post('rating/{test}', [RatingController::class, 'rate'])->name('rating.rate');
    //game
    route::get('game/{resultItem}/config/{mode}', [GameController::class, 'mapConfig'])->name('game.mapConfig');
    route::get('game/{result}/summary', [GameController::class, 'summary'])->name('game.summary');
    route::get('game/{result}/question/{resultItem}', [GameController::class, 'getQuestion'])->name('game.getQuestion');
    route::post('game/{result}/question/{resultItem}', [GameController::class, 'submitQuestion'])->name('game.submitQuestion');
    route::get('game/{test}', [GameController::class, 'start'])->name('game.start');
    //test-bank
    route::get('test/{test}/banks', [TestController::class, 'addBank'])->name('test.addBank');
    route::get('test/{test}/bank/{bank}/edit', [TestController::class, 'changeBankCount'])->name('test.changeBankCount');
    route::put('test/{test}/bank/{bank}/edit', [TestController::class, 'updateBankCount'])->name('test.updateBankCount');
    route::post('test/{test}/bank/{bank}', [TestController::class, 'saveBank'])->name('test.saveBank');
    Route::delete('/test/{test}/bank/{bank}', [TestController::class, 'removeBank'])->name('test.removeBank');
    //map
    Route::get('map/{map}/config', [MapController::class, 'getConfig'])->name('map.config'); 
    //resources
    Route::post('test/{test}/unpublish', [TestController::class, 'unpublish'])->name('test.unpublish');
    Route::post('test/{test}/publish', [TestController::class, 'publish'])->name('test.publish');
    Route::resource('test', TestController::class);
    Route::post('bank/{bank}/publish', [BankController::class, 'publish'])->name('bank.publish');
    Route::resource('bank', BankController::class); 
    Route::resource('question', QuestionController::class);
    //auth
    Route::post('/logout', [AuthController::class, 'logout']) ->name('logout');  
    //bank-question
    Route::get('/bank/{bank}/questions', [BankController::class, 'addQuestion']) ->name('addQuestion');
    Route::post('/question/{question}/bank/{bank}', [QuestionController::class, 'addToBank'])->name('question.addToBank');
    Route::delete('/question/{question}/bank/{bank}', [QuestionController::class, 'removeFromBank'])->name('question.removeFromBank');
    //test-question
    Route::get('/test/{test}/questions', [TestController::class, 'addQuestion']) ->name('addQuestionToTest'); 
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
    route::get('/', [TestController::class, 'availableTestIndex'])->name('home');  
});