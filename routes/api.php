<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\LessonUserProgressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseProgressController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\QuizController;
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
    Route::get('users/{id}/details',[UserController::class,'showDetailDetails']);
});

 /******************* Cours Routes ****************************** */
Route::middleware(['auth:sanctum', 'role:admin,formateur'])->group(function () {
    Route::get('courses/{id}', [CourseController::class, 'show']);
        Route::get('courses/user/{id}', [CourseController::class, 'getCoursByFormateur']);
    Route::put('courses/{course}', [CourseController::class, 'update']);
    Route::post('courses', [CourseController::class, 'store']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('courses/{id}', [CourseController::class, 'show']);
    Route::post('courses/{id}/start', [CourseController::class, 'startCourse']);
});
Route::get('courses', [CourseController::class, 'index']);


 /******************* lessons Routes ****************************** */
Route::middleware(['auth:sanctum', 'role:admin,formateur'])->group(function () {
    Route::post('courses/{course}/lessons', [LessonController::class, 'store']);
    Route::put('lessons/{lesson}', [LessonController::class, 'update']);
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('courses/{course}/lessons/{lesson}', [LessonController::class, 'show']);
    Route::get('courses/{course}/lessons', [LessonController::class, 'index']);
    Route::patch('lessons/{lesson}/progress', [LessonUserProgressController::class, 'updateProgress']);
});


 /******************* Resources Routes ****************************** */
Route::middleware(['auth:sanctum', 'role:admin,formateur'])->group(function () {
    Route::post('lesson/{lesson}/resources', [ResourceController::class, 'store']);
    Route::put('lesson/{lesson}/resources/{resource}', [ResourceController::class, 'update']);
    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('lesson/{lesson}/resources', [ResourceController::class, 'index']);
    Route::get('lesson/{lesson}/resources/{resource}', [ResourceController::class, 'show']); 
});


 /******************* Evaluations Routes ****************************** */
Route::post('/lesson/{lesson}/evaluations', [EvaluationController::class, 'store']);
Route::get('/courses/{course}/evaluations', [EvaluationController::class, 'index']);
Route::post('/evaluations/{evaluation}/submit', [EvaluationController::class, 'submit']);

/******************* Quiz Routes ****************************** */
Route::middleware(['auth:sanctum','role:admin,formateur'])->group(function () {
    Route::post('courses/{course}/quizzes', [QuizController::class, 'store']);
});
Route::middleware(['auth:sanctum', 'role:admin,formateur,apprenant'])->group(function () {
    Route::get('courses/{course}/quizzes', [QuizController::class, 'index']);
    Route::post('courses/{course}/quizzes/submit', [QuizController::class, 'submit']);
});

 /******************* Progresssion Routes ****************************** */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/courses/{courseId}/progress', [CourseProgressController::class, 'getProgress']);
    Route::post('/courses/{courseId}/progress', [CourseProgressController::class, 'updateProgress']);
});

 /******************* Certificates Routes ****************************** */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('certificates/{id}', [CertificateController::class, 'show']);
    Route::post('certificates/generate', [CertificateController::class, 'generate']);
});


