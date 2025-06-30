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
     public function store(Request $request, Course $course) {

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

    public function submit(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_id' => 'required|exists:answers,id',
        ]);

        $quiz = Quiz::with('questions.answers')->findOrFail($data['quiz_id']);
        // dd($quiz->toArray());
        $correctCount = 0;
        $total = count($quiz->questions);
        $results = [];

        // 1. Créer un enregistrement dans user_quizzes
        $userQuiz = $user->userQuizzes()->create([
            'quiz_id' => $quiz->id,
            'score' => 0, // provisoire
        ]);
        foreach ($data['answers'] as $response) {
            $question = $quiz->questions->firstWhere('id', $response['question_id']);
            $selectedAnswer = $question->answers->firstWhere('id', $response['answer_id']);

            $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
            if ($isCorrect) $correctCount++;

            // 2. Enregistrer la réponse dans user_answers
            $userQuiz->answers()->create([
                'question_id' => $question->id,
                'answer_id' => $selectedAnswer?->id,
                'is_correct' => $isCorrect,
            ]);

            $results[] = [
                'question_id' => $question->id,
                'correct_answer_id' => $question->answers->firstWhere('is_correct', true)?->id,
                'selected_answer_id' => $selectedAnswer?->id,
                'is_correct' => $isCorrect
            ];
        }

        $score = round(($correctCount / $total) * 100, 2);

        // 3. Mettre à jour le score dans user_quizzes
        $userQuiz->update(['score' => $score]);

        return response()->json([
            'message' => 'Quiz soumis avec succès',
            'score' => $correctCount . '/' . $total,
            'percentage' => $score,
            'results' => $results
        ]);
    }

        


}
