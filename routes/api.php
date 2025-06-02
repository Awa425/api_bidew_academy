<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route publique 
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [SocialAuthController::class, 'handleGoogleLogin']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'redirectToGoogle']);

// User Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('profile', function (Request $request) {return $request->user(); });
    Route::post('logout', [AuthController::class, 'logout']);
});

// Cours Routes
Route::middleware(['auth:sanctum', 'role:admin,formateur'])->group(function () {
    Route::get('courses/{id}', [CourseController::class, 'show']);
    Route::post('courses', [CourseController::class, 'store']);
    Route::put('courses/{id}', [CourseController::class, 'update']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('courses/{id}', [CourseController::class, 'show']);
});
Route::get('courses', [CourseController::class, 'index']);

// Lessons routes
Route::middleware(['auth:sanctum', 'role:admin,formateur'])->group(function () {
    Route::post('courses/{course}/lessons', [LessonController::class, 'store']);
    Route::put('lessons/{lesson}', [LessonController::class, 'update']);
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy']);
});
Route::get('courses/{course}/lessons', [LessonController::class, 'index']);
Route::get('courses/{course}/lessons/{lesson}', [LessonController::class, 'show']);

// Routes pour les ressources (authentification requise)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('lesson/{lesson}/resources', [ResourceController::class, 'store']);
    Route::put('cours/{cours}/lesson/{lesson}/resources/{resource}', [ResourceController::class, 'update']);
    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy']);
    Route::post('/lesson/{lesson}/evaluations', [EvaluationController::class, 'store']);
});
// Routes publiques pour les ressources
Route::get('lesson/{lesson}/resources', [ResourceController::class, 'index']);
Route::get('lesson/{lesson}/resources/{resource}', [ResourceController::class, 'show']);

// Routes pour les évaluations
Route::get('/courses/{course}/evaluations', [EvaluationController::class, 'index']);
Route::post('/evaluations/{evaluation}/submit', [EvaluationController::class, 'submit']);

// Route::post('/auth/google-login', [SocialAuthController::class, 'handleGoogleCallback']);