<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseUserProgress;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
public function index($coursId)
{
    $user = auth()->user();

    dd($user);
    // Si l'utilisateur a le rôle "apprenant", on vérifie sa progression
    if ($user->role == 'apprenant') {
        $progress = CourseUserProgress::where('user_id', $user->id)
            ->where('course_id', $coursId)
            ->first();

        if (!$progress || $progress->progression < 100) {
            return response()->json([
                'message' => 'Vous devez compléter toutes les leçons du cours avant d’accéder au quiz.',
            ], 403);
        }
    }

    // Si admin, formateur ou apprenant avec progression à 100%, on retourne le quiz
    $quizzes = Quiz::with('questions.answers')
        ->where('course_id', $coursId)
        ->get();

    return response()->json($quizzes);
}


     public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id'
        ]);
        return Quiz::create($validated);
    }
}
