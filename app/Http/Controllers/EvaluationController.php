<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *      name="Evaluations",
 *      description="API Endpoints of Evaluations"
 * )
 */

class EvaluationController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/courses/{course_id}/evaluations",
     *      operationId="createEvaluation",
     *      tags={"Evaluations"},
     *      summary="Create new evaluation",
     *      description="Create a new evaluation for a specific course",
     *      security={
     *          {"bearerAuth": {}}
     *      },
     *      @OA\Parameter(
     *          name="course_id",
     *          description="Course ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"type", "title", "questions", "total_points", "passing_score", "time_limit_minutes"},
     *              @OA\Property(property="type", type="string", enum={"quiz", "qcm", "essay"}, example="qcm"),
     *              @OA\Property(property="title", type="string", format="string", example="Final Exam"),
     *              @OA\Property(property="description", type="string", format="string", example="Final exam for the course"),
     *              @OA\Property(
     *                  property="questions",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="question", type="string", example="What is the capital of France?"),
     *                      @OA\Property(property="type", type="string", enum={"mcq", "tf", "essay"}, example="mcq"),
     *                      @OA\Property(
     *                          property="options",
     *                          type="array",
     *                          @OA\Items(type="string", example="Paris"),
     *                          description="Required for mcq type questions"
     *                      ),
     *                      @OA\Property(property="correct_answer", type="string", example="Paris"),
     *                      @OA\Property(property="points", type="integer", example="10")
     *                  )
     *              ),
     *              @OA\Property(property="total_points", type="integer", example="100"),
     *              @OA\Property(property="passing_score", type="integer", example="60"),
     *              @OA\Property(property="time_limit_minutes", type="integer", example="60")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Evaluation created successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Evaluation"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
    public function store(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:quiz,qcm,essay',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:mcq,tf,essay',
            'questions.*.options' => 'required_if:questions.*.type,mcq|array',
            'questions.*.correct_answer' => 'required|string',
            'total_points' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1',
            'time_limit_minutes' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $evaluation = $course->evaluations()->create($request->all());
        return response()->json($evaluation, 201);
    }

    /**
     * @OA\Get(
     *      path="/api/courses/{course_id}/evaluations",
     *      operationId="getEvaluations",
     *      tags={"Evaluations"},
     *      summary="Get all evaluations for a course",
     *      description="Returns list of evaluations for a specific course",
     *      @OA\Parameter(
     *          name="course_id",
     *          description="Course ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      @OA\Items(ref="#/components/schemas/Evaluation")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index(Course $course)
    {
        $evaluations = $course->evaluations()->with(['questions'])->get();
        return response()->json($evaluations);
    }

    /**
     * @OA\Post(
     *      path="/api/evaluations/{evaluation_id}/submit",
     *      operationId="submitEvaluation",
     *      tags={"Evaluations"},
     *      summary="Submit evaluation attempt",
     *      description="Submit an evaluation attempt with answers",
     *      security={
     *          {"bearerAuth": {}}
     *      },
     *      @OA\Parameter(
     *          name="evaluation_id",
     *          description="Evaluation ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"answers", "time_taken_minutes"},
     *              @OA\Property(
     *                  property="answers",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="question_id", type="integer", example="1"),
     *                      @OA\Property(property="answer", type="string", example="Paris")
     *                  )
     *              ),
     *              @OA\Property(property="time_taken_minutes", type="integer", example="30")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Evaluation submitted successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      type="object",
     *                      @OA\Property(property="score", type="integer", example="85"),
     *                      @OA\Property(property="total_points", type="integer", example="100"),
     *                      @OA\Property(property="is_passed", type="boolean", example="true"),
     *                      @OA\Property(property="time_taken_minutes", type="integer", example="30")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
    public function submit(Request $request, Evaluation $evaluation)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|string',
            'time_taken_minutes' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Calculer le score
        $score = 0;
        $totalPoints = 0;
        $questions = $evaluation->questions;

        foreach ($request->answers as $answer) {
            $question = $questions->find($answer['question_id']);
            if ($question && $question->correct_answer === $answer['answer']) {
                $score += $question->points;
            }
            $totalPoints += $question->points;
        }

        // Créer l'attempt
        $attempt = $evaluation->attempts()->create([
            'user_id' => auth()->id(),
            'score' => $score,
            'total_points' => $totalPoints,
            'time_taken_minutes' => $request->time_taken_minutes,
            'is_passed' => $score >= $evaluation->passing_score
        ]);

        return response()->json($attempt);
    }
}
