<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route publique
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Access : Auth
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
     // Exemples : accès aux données sécurisées
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
});

// Access : Auth et Role Admin
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('courses', CourseController::class);
});

// Routes publiques pour les cours
// Route::get('/courses', [CourseController::class, 'index']);
// Route::get('/courses/{course}', [CourseController::class, 'show']);

