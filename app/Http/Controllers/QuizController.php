<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseUserProgress;
use App\Models\Lesson;
use App\Models\LessonUserProgress;
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
            
            if (!$progress || $progress->progress_percent < 90) {
                return response()->json([
                    'message' => 'Vous devez compléter toutes les leçons du cours avant d’accéder au quiz. Vous etes a '. $progress->progress_percent .' %',
                ], 403);
            }
        }

        // Si admin, formateur ou apprenant avec progression à 100%, on retourne le quiz
        $quizzes = Quiz::with('questions.answers')
            ->where('course_id', $coursId)
            ->orderBy('id','desc')
            ->get();

        return response()->json($quizzes->load('questions.answers'));
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
     public function store_without_validated_15_question(Request $request, Course $course) {

        $userId = auth()->id();

        $courseUserId = $course->user_id;
        if ($userId !== $courseUserId) {
            return response()->json([
                'error' => 'Vous n\'avez pas la permission de publier une cette evaluation pour ce cours.',
                'debug' => [
                    'current_user_id' => $userId,
                    'course_user_id' => $courseUserId
                    ]
                ], 403);
        }
        
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
    public function store(Request $request, Course $course)
{
    $userId = auth()->id();

    // Vérifier que seul le propriétaire du cours peut créer le quiz
    if ($userId !== $course->user_id) {
        return response()->json([
            'error' => 'Vous n\'avez pas la permission de publier une évaluation pour ce cours.',
            'debug' => [
                'current_user_id' => $userId,
                'course_user_id' => $course->user_id
            ]
        ], 403);
    }

    // Validation basique des champs
    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
        'description' => 'nullable|string',
        'questions' => 'required|array',
        'questions.*.type' => 'required|in:multiple_choice,single_choice,text',
        'questions.*.text' => 'required|string',
        'questions.*.answers' => 'required|array|min:1',
        'questions.*.answers.*.text' => 'required|string',
        'questions.*.answers.*.is_correct' => 'required|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Vérifier qu'il y a minimum 20 questions
    if (count($request->questions) < 20) {
        return response()->json([
            'error' => 'Chaque quiz doit contenir minimum 30 questions.',
            'nb_questions_envoyees' => count($request->questions)
        ], 422);
    }

    // Création du quiz
    $quiz = $course->quizzes()->create([
        'title' => $request->title,
        'description' => $request->description,
    ]);

    // Sauvegarde des 15 questions et de leurs réponses
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
        'message' => 'Quiz créé avec succès avec minimum 30 questions.',
        'quiz' => $quiz->load('questions.answers')
    ], 201);
}


    /**
     * @OA\Post(
     *     path="/api/courses/{course}/quizzes/submit",
     *     summary="Soumettre un quiz avec les réponses de l'utilisateur",
     *     description="Soumet les réponses de l'utilisateur à un quiz, enregistre les résultats, et calcule le score.",
     *     operationId="submitQuiz",
     *     tags={"Quiz"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quiz_id", "answers"},
     *             @OA\Property(property="quiz_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="answers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="question_id", type="integer", example=10),
     *                     @OA\Property(
     *                         property="answer_ids",
     *                         type="array",
     *                         @OA\Items(type="integer", example=42)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quiz soumis avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Quiz soumis avec succès"),
     *             @OA\Property(property="score", type="string", example="3/5"),
     *             @OA\Property(property="percentage", type="number", format="float", example=60.00),
     *             @OA\Property(
     *                 property="results",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="question_id", type="integer", example=10),
     *                     @OA\Property(property="correct_answer_ids", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="selected_answer_ids", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="is_correct", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Déjà effectué"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Quiz non trouvé"
     *     )
     * )
     */
public function submit(Request $request)
{
    $user = auth()->user();

    $data = $request->validate([
        'quiz_id' => 'required|exists:quizzes,id',
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|exists:questions,id',
        'answers.*.answer_ids' => 'required|array',
        'answers.*.answer_ids.*' => 'integer|exists:answers,id'
    ]);

    $quiz = Quiz::with('questions.answers', 'course')->findOrFail($data['quiz_id']);

    // 🔐 Empêcher de refaire le quiz
    $alreadyAttempted = $user->userQuizzes()->where('quiz_id', $quiz->id)->exists();
    if ($alreadyAttempted) {
        return response()->json([
            'message' => 'Vous avez déjà soumis ce quiz.'
        ], 403);
    }

    //  Création de l'entrée UserQuiz
    $userQuiz = $user->userQuizzes()->create([
        'quiz_id' => $quiz->id,
        'score' => 0
    ]);

    $correctCount = 0;
    $results = [];

    foreach ($data['answers'] as $response) {
        $question = $quiz->questions->firstWhere('id', $response['question_id']);
        if (!$question) continue;

        $correctAnswerIds = $question->answers->where('is_correct', true)->pluck('id')->sort()->values();
        $selectedAnswerIds = collect($response['answer_ids'])->sort()->values();

        $isCorrect = $selectedAnswerIds->all() === $correctAnswerIds->all();
        if ($isCorrect) $correctCount++;

        // ⏺️ Sauvegarde dans user_answers
        foreach ($selectedAnswerIds as $answerId) {
            $userQuiz->answers()->create([
                'question_id' => $question->id,
                'answer_id' => $answerId,
                'is_correct' => $isCorrect
            ]);
        }

        $results[] = [
            'question_id' => $question->id,
            'selected_answer_ids' => $selectedAnswerIds,
            'correct_answer_ids' => $correctAnswerIds,
            'is_correct' => $isCorrect
        ];
    }

    $total = count($quiz->questions);
    $score = $total > 0 ? round(($correctCount / $total) * 100, 2) : 0;

    // 📝 Mise à jour du score
    $userQuiz->update(['score' => $score]);

    // 📈 Mise à jour de course_user_progress
    $progress = CourseUserProgress::firstOrNew([
        'user_id' => $user->id,
        'course_id' => $quiz->course_id
    ]);

    // S'assurer que les leçons sont déjà prises en compte
    $totalLessons = $quiz->course->lessons()->count();
    $completedLessons = LessonUserProgress::where('user_id', $user->id)
        ->whereHas('lesson', fn($q) => $q->where('course_id', $quiz->course_id))
        ->where('is_completed', true)
        ->count();

    $quizWeight = 1; // Chaque quiz compte comme 1 unité
    $totalUnits = $totalLessons + $quizWeight;
    $completedUnits = $completedLessons + 1; // Ajout du quiz comme terminé

    $progress->progress_percent = round(($completedUnits / $totalUnits) * 100, 2);
    $progress->save();

    return response()->json([
        'message' => 'Quiz soumis avec succès',
        'score' => "$correctCount / $total",
        'percentage' => $score,
        'results' => $results,
        'progress_percent' => $progress->progress_percent
    ]);
}


        


}
