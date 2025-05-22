<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class EvaluationController extends Controller
{
  
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

   
    public function index(Course $course)
    {
        $evaluations = $course->evaluations()->with(['questions'])->get();
        return response()->json($evaluations);
    }

   
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
