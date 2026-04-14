<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/topic/{slug}', [TopicController::class, 'show'])->name('topic.show');
Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
Route::get('/interview', [QuizController::class, 'interview'])->name('quiz.interview');
