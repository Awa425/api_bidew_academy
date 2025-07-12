<?php

namespace App\Http\Controllers;

use App\Models\LessonUserProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{   
        /**
     * @OA\Get(
     *     path="/api/users/{id}/details",
     *     summary="Detail complet d'un user",
     *     tags={"User"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détail d'un user",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */
    public function showDetailDetails($id)
    {
        $user = User::with([
            'courseProgress.course',
            'lessonProgress.lesson' => function ($query) {
                $query->with('course');
            },
            'userQuizzes.quiz.questions.answers' // chargement des quiz et de leurs relations
        ])->findOrFail($id);

        // Formatage des cours
        $courses = $user->courseProgress->map(function ($progress) {
            return [
                'course_id' => $progress->course->id,
                'course_title' => $progress->course->title,
                'progress_percent' => $progress->progress_percent,
            ];
        });

        // Leçons complétées
        $lessonProgress = $user->lessonProgress->map(function ($progress) {
            return [
                'lesson_id' => $progress->lesson->id,
                'lesson_title' => $progress->lesson->title,
                'course_title' => $progress->lesson->course->title,
                'is_locked' => $progress->is_locked,
                'is_completed' => $progress->is_completed,
                'completed_at' => $progress->updated_at->toDateTimeString(),
            ];
        });
        // Quiz complétés
        $quizzes = $user->userQuizzes->map(function ($userQuiz) {
            return [
                'quiz_id' => $userQuiz->quiz->id,
                'quiz_title' => $userQuiz->quiz->title,
                'score' => $userQuiz->score,
                'questions' => $userQuiz->quiz->questions->map(function ($question) use ($userQuiz) {
                    $userAnswer = $userQuiz->answers->firstWhere('question_id', $question->id);
                    $correctAnswer = $question->answers->firstWhere('is_correct', true);
                    return [
                        'question_id' => $question->id,
                        'question_text' => $question->text,
                        'correct_answer_id' => $correctAnswer?->id,
                        'correct_answer_text' => $correctAnswer?->text,
                        'selected_answer_id' => $userAnswer?->answer_id,
                        'is_correct' => $userAnswer?->is_correct,
                    ];
                }),
            ];
        });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'courses_progress' => $courses,
            'lessons_progress' => $lessonProgress,
            'quizzes_attempted' => $quizzes,
        ]);
    }

    public function show($id)
    {
        return response()->json(LessonUserProgress::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,formateur,apprenant',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }
}
