<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/questions', [QuestionController::class, 'index']);
Route::get('/questions/{question}', [QuestionController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('questions', QuestionController::class)->except(['index', 'show']);

    // Responses
    Route::post('/questions/{question}/responses', [ResponseController::class, 'store']);
    Route::put('/responses/{response}', [ResponseController::class, 'update']);
    Route::delete('/responses/{response}', [ResponseController::class, 'destroy']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/questions/{question}/favorite', [FavoriteController::class, 'toggle']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);

    // Admin dashboard stats (admin role only)
    Route::get('/admin/stats', [AdminController::class, 'stats']);
});
