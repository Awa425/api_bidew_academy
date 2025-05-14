<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\EvaluationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route publique 
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Access : Auth
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('profile', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('courses/{id}', [CourseController::class, 'show']);
});

// Access : Auth et Role Admin
Route::middleware(['auth:sanctum', 'role:admin,formateur'])->group(function () {
    Route::apiResource('users', UserController::class);

    // Routes pour les cours
    Route::post('courses', [CourseController::class, 'store']);
    Route::put('courses/{id}', [CourseController::class, 'update']);
});

// Route publique pour Courses
Route::get('courses', [CourseController::class, 'index']);


// Routes publiques pour les leçons
Route::get('/courses/{course}/lessons', [LessonController::class, 'index']);
Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show']);

// Routes publiques pour les ressources
Route::get('/courses/{course}/resources', [ResourceController::class, 'index']);
Route::get('/courses/{course}/resources/{resource}', [ResourceController::class, 'show']);

// Routes pour les leçons (authentification requise)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/courses/{course}/lessons', [LessonController::class, 'store']);
    Route::put('/lessons/{lesson}', [LessonController::class, 'update']);
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy']);
});

// Routes pour les ressources (authentification requise)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/courses/{course}/resources', [ResourceController::class, 'store']);
    Route::put('/resources/{resource}', [ResourceController::class, 'update']);
    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy']);
});

// Routes pour les évaluations
Route::post('/courses/{course}/evaluations', [EvaluationController::class, 'store']);
Route::get('/courses/{course}/evaluations', [EvaluationController::class, 'index']);
Route::post('/evaluations/{evaluation}/submit', [EvaluationController::class, 'submit']);

