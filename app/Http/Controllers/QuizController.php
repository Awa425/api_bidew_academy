<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseUserProgress;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/courses/{course}/quizzes",
     *     summary="Lister tous les quiz",
     *     tags={"Quiz"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des quiz",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Quiz"))
     *     )
     * )
     */
    public function index($coursId)
    { 
        $user = auth()->user();

        // Si l'utilisateur a le rôle "apprenant", on vérifie sa progression
        if ($user->role == 'apprenant') {
            $progress = CourseUserProgress::where('user_id', $user->id)
            ->where('course_id', $coursId)
            ->first();
            
            if (!$progress || $progress->progress_percent < 100) {
                return response()->json([
                    'message' => 'Vous devez compléter toutes les leçons du cours avant d’accéder au quiz. Vous etes a '. $progress->progress_percent .' %',
                ], 403);
            }
        }

        // Si admin, formateur ou apprenant avec progression à 100%, on retourne le quiz
        $quizzes = Quiz::with('questions.answers')
            ->where('course_id', $coursId)
            ->get();

        return response()->json($quizzes->load('questions'));
    }

        /**
     * @OA\Post(
     *      path="/api/courses/{course}/quizzes",
     *      summary="Création de nouvelle quiz",
     *      tags={"Quiz"},
     *      security={{"sanctumAuth":{}}},
     *      @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Quiz")
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Leçon créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Quiz")
     *     ),
     *     @OA\Response(response=400, description="Données invalides")
     * )
     */
     public function store(Request $request, Course $course) {
        
        $validator = Validator::make($request->all(),[
        'title' => 'required|string',
        'description' => 'nullable|string',
        'questions' => 'required|array|min:1',
        'questions.*.type' => 'required|in:multiple_choice,single_choice,text',
        'questions.*.text' => 'required|string',
        'questions.*.answers' => 'required|array|min:1',
        'questions.*.answers.*.text' => 'required|string',
        'questions.*.answers.*.is_correct' => 'required|boolean',
        ]);
  
            if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Creation Quizz
        $quiz = $course->quizzes()->create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        foreach ($request->questions as $questionData) {
            $question = $quiz->questions()->create([
                'type' => $questionData['type'],
                'text' => $questionData['text'],
             ]);

            foreach ($questionData['answers'] as $answerData) {
                $question->answers()->create([
                    'text' => $answerData['text'],
                    'is_correct' => $answerData['is_correct'],
                ]);
        }
        }

        return response()->json([
            'Quiz'=>$quiz->load('questions.answers')], 201);
        }
}
