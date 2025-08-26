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

public function index($coursId) { 
    $user = auth()->user(); 

    if ($user->role == 'apprenant') { 
        $progress = CourseUserProgress::where('user_id', $user->id) ->where('course_id', $coursId) ->first(); 

        if (!$progress || $progress->progress_percent < 90) { 
        return response()->json([ 'message' => 'Vous devez compléter toutes les leçons du cours avant d’accéder au quiz. Vous etes a '. $progress->progress_percent .' %', ], 403); } } 

       $quizzes = Quiz::with('questions.answers') ->where('course_id', $coursId) ->orderBy('id','desc') ->get(); return response()->json($quizzes->load('questions.answers')); 
    }

     public function index_15_quiz($coursId) 
{
    $user = auth()->user();

    // Vérification de la progression pour les apprenants
    if ($user->role === 'apprenant') {
        $progress = CourseUserProgress::where('user_id', $user->id)
            ->where('course_id', $coursId)
            ->first();

        if (!$progress || $progress->progress_percent < 90) {
            return response()->json([
                'message' => 'Vous devez compléter toutes les leçons du cours avant d’accéder au quiz. Vous êtes à '. ($progress->progress_percent ?? 0) .' %',
            ], 403);
        }
    }

    // Charger uniquement 15 questions aléatoires par quiz
    $quizzes = Quiz::with([
        'questions' => function ($query) {
            $query->inRandomOrder()->limit(15)->with('answers');
        }
    ])
    ->where('course_id', $coursId)
    ->orderBy('id', 'desc')
    ->get();

    return response()->json($quizzes);
}

/**
 * @OA\Post(
 *     path="/api/courses/{course}/quizzes",
 *     summary="Créer un quiz pour un cours",
 *     description="Permet à l’utilisateur (formateur) d’ajouter un quiz avec 15 à 30 questions et leurs réponses",
 *     tags={"Quiz"},
 *     security={{"sanctumAuth":{}}},
 * 
 *     @OA\Parameter(
 *         name="course",
 *         in="path",
 *         required=true,
 *         description="ID du cours",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 * 
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"title","questions"},
 *             @OA\Property(property="title", type="string", example="Quiz Avancé Programmation"),
 *             @OA\Property(property="description", type="string", example="Évaluation approfondie des connaissances en programmation"),
 *             @OA\Property(
 *                 property="questions",
 *                 type="array",
 *                 minItems=15,
 *                 maxItems=30,
 *                 @OA\Items(
 *                     type="object",
 *                     required={"type","text","answers"},
 *                     @OA\Property(property="type", type="string", enum={"single_choice","multiple_choice","text"}, example="single_choice"),
 *                     @OA\Property(property="text", type="string", example="Quel est le mot-clé pour définir une fonction en PHP ?"),
 *                     @OA\Property(
 *                         property="answers",
 *                         type="array",
 *                         minItems=1,
 *                         @OA\Items(
 *                             type="object",
 *                             required={"text","is_correct"},
 *                             @OA\Property(property="text", type="string", example="function"),
 *                             @OA\Property(property="is_correct", type="boolean", example=true)
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=201,
 *         description="Quiz créé avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="Quiz", type="object",
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="title", type="string", example="Quiz Avancé Programmation"),
 *                 @OA\Property(property="description", type="string", example="Évaluation approfondie des connaissances"),
 *                 @OA\Property(property="questions", type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=101),
 *                         @OA\Property(property="type", type="string", example="single_choice"),
 *                         @OA\Property(property="text", type="string", example="Quel est le mot-clé pour définir une fonction en PHP ?"),
 *                         @OA\Property(property="answers", type="array",
 *                             @OA\Items(
 *                                 type="object",
 *                                 @OA\Property(property="id", type="integer", example=501),
 *                                 @OA\Property(property="text", type="string", example="function"),
 *                                 @OA\Property(property="is_correct", type="boolean", example=true)
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé (seul le formateur du cours peut ajouter un quiz)"
 *     ),
 * 
 *     @OA\Response(
 *         response=422,
 *         description="Erreur de validation (format du payload incorrect)"
 *     )
 * )
 */

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

        public function get_all_quis($coursId)
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
 *     path="/api/courses/{course}/quizzes/submit",
 *     summary="Soumettre un quiz et calculer le score",
 *     description="Permet à un utilisateur (apprenant) de soumettre ses réponses à un quiz. 
 *                  Le quiz doit contenir 15 questions, et au moins 12 bonnes réponses (80%) 
 *                  sont nécessaires pour valider.",
 *     tags={"Quiz"},
 *     security={{"sanctumAuth":{}}},
 *     @OA\Parameter(
 *         name="course",
 *         in="path",
 *         required=true,
 *         description="ID du cours",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"quiz_id", "answers"},
 *             @OA\Property(
 *                 property="quiz_id",
 *                 type="integer",
 *                 example=1,
 *                 description="ID du quiz auquel l'utilisateur répond"
 *             ),
 *             @OA\Property(
 *                 property="answers",
 *                 type="array",
 *                 description="Liste des réponses de l'utilisateur aux questions du quiz",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"question_id", "answer_ids"},
 *                     @OA\Property(
 *                         property="question_id",
 *                         type="integer",
 *                         example=101,
 *                         description="ID de la question"
 *                     ),
 *                     @OA\Property(
 *                         property="answer_ids",
 *                         type="array",
 *                         description="IDs des réponses choisies (vide si question de type 'text')",
 *                         @OA\Items(type="integer", example=501)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=200,
 *         description="Quiz soumis avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Quiz soumis avec succès"),
 *             @OA\Property(property="score", type="string", example="12 / 15"),
 *             @OA\Property(property="percentage", type="number", example=80.0),
 *             @OA\Property(
 *                 property="results",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="question_id", type="integer", example=101),
 *                     @OA\Property(property="selected_answer_ids", type="array", @OA\Items(type="integer")),
 *                     @OA\Property(property="correct_answer_ids", type="array", @OA\Items(type="integer")),
 *                     @OA\Property(property="is_correct", type="boolean", example=true)
 *                 )
 *             ),
 *             @OA\Property(property="progress_percent", type="number", example=100)
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé ou quiz déjà soumis",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Vous avez déjà soumis ce quiz.")
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=422,
 *         description="Erreur de validation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="errors", type="object")
 *         )
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

    // Vérifier si l'utilisateur a déjà validé ce quiz
    $alreadyValidated = $user->userQuizzes()
        ->where('quiz_id', $quiz->id)
        ->where('score', '>=', 80)
        ->exists();

    if ($alreadyValidated) {
        return response()->json([
            'message' => 'Vous avez déjà validé ce quiz et ne pouvez plus le repasser.'
        ], 403);
    }

    // Créer une nouvelle tentative (même s'il a échoué auparavant)
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

    // Toujours basé sur 15 questions
    $totalQuestions = 15;
    $score = round(($correctCount / $totalQuestions) * 100, 2);

    // Mise à jour du score de cette tentative
    $userQuiz->update(['score' => $score]);

    // Validation : minimum 12 bonnes réponses sur 15
    $validated = $correctCount >= 12;

    if (!$validated) {
        return response()->json([
            'message' => "Échec : Vous devez obtenir au moins 80% (12 bonnes réponses sur 15).",
            'score' => "$correctCount / $totalQuestions",
            'percentage' => $score,
            'results' => $results,
            'validated' => false
        ], 403);
    }

    // Si validé → mise à jour progression à 100 %
    $progress = CourseUserProgress::firstOrNew([
        'user_id' => $user->id,
        'course_id' => $quiz->course_id
    ]);
    $progress->progress_percent = 100;
    $progress->save();

    return response()->json([
        'message' => 'Quiz validé avec succès 🎉',
        'score' => "$correctCount / $totalQuestions",
        'percentage' => $score,
        'results' => $results,
        'validated' => true,
        'progress_percent' => $progress->progress_percent
    ]);
}



public function submit_old(Request $request)
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

    // Empêcher de refaire le quiz
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

    // ⚠️ Vérification du seuil de validation (80%)
    $minimumCorrect = ceil($total * 0.8); // arrondi au supérieur
    $validated = $correctCount >= $minimumCorrect;

    if (!$validated) {
        return response()->json([
            'message' => "Échec  : Vous devez obtenir au moins 80% ($minimumCorrect bonnes réponses sur $total).",
            'score' => "$correctCount / $total",
            'percentage' => $score,
            'results' => $results,
            'validated' => false
        ], 403);
    }

    // Mise à jour de course_user_progress
    $progress = CourseUserProgress::firstOrNew([
        'user_id' => $user->id,
        'course_id' => $quiz->course_id
    ]);

    // si le quiz est validé → cours terminé à 100%
    $progress->progress_percent = 100;
    $progress->save();

    return response()->json([
        'message' => 'Quiz validé avec succès 🎉',
        'score' => "$correctCount / $total",
        'percentage' => $score,
        'results' => $results,
        'validated' => true,
        'progress_percent' => $progress->progress_percent
    ]);
}



        


}
