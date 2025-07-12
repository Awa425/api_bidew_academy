<?php

namespace App\Http\Controllers;

use App\Models\CourseUserProgress;
use App\Models\Lesson;
use App\Models\LessonUserProgress;
use Illuminate\Http\Request;

class LessonUserProgressController extends Controller
{

    
    /**
     * @OA\Patch(
     *     path="/api/lessons/{lesson}/progress",
     *     summary="Marquer une leçon comme terminée et mettre à jour la progression du cours",
     *     description="Cette méthode permet à un utilisateur authentifié de marquer une leçon comme complétée, débloquer la prochaine leçon et mettre à jour la progression globale du cours.",
     *     operationId="updateLessonProgress",
     *     tags={"Leçon"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="lesson",
     *         in="path",
     *         required=true,
     *         description="ID de la leçon à marquer comme terminée",
     *         @OA\Schema(type="integer", example=7)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Progression mise à jour avec succès.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Progression mise à jour avec succès."),
     *             @OA\Property(property="completed_lesson_id", type="integer", example=7),
     *             @OA\Property(property="next_unlocked_lesson_id", type="integer", nullable=true, example=8),
     *             @OA\Property(property="course_progress_percent", type="number", format="float", example=42.86)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Utilisateur non authentifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Non authentifié.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Leçon introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Leçon non trouvée.")
     *         )
     *     )
     * )
     */

     public function updateProgress(Request $request, $lessonId)
    {
        $userId = auth()->id();

        $lesson = Lesson::with('course')->findOrFail($lessonId);
        $course = $lesson->course;

        // 1. Marquer la leçon comme complétée
        LessonUserProgress::updateOrCreate(
            ['lesson_id' => $lesson->id, 'user_id' => $userId],
            [
                'is_locked' => false,
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        // 2. Récupération ou création de la progression du cours
        $progress = CourseUserProgress::firstOrNew([
            'user_id' => $userId,
            'course_id' => $course->id
        ]);

        // 3. Leçons complétées
        $completedLessons = LessonUserProgress::where('user_id', $userId)
            ->whereHas('lesson', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $totalLessons = Lesson::where('course_id', $course->id)->count();

        // 4. Vérifie si le quiz a été complété
        $hasQuiz = $course->quizzes()->exists();
        $quizCompleted = false;

        if ($hasQuiz) {
            $quiz = $course->quizzes()->first(); // On suppose 1 quiz par cours
            $quizCompleted = $quiz->userAttempts()->where('user_id', $userId)->exists();
        }

        // 5. Calcul du pourcentage de progression
        $lessonPart = $totalLessons > 0
            ? (count($completedLessons) / $totalLessons)
            : 0;

        $quizPart = $hasQuiz ? ($quizCompleted ? 1 : 0) : 1; // 1 si pas de quiz

        $progressPercent = round(($lessonPart * 0.9 + $quizPart * 0.1) * 100, 2); // Ex: 90% pour les leçons, 10% pour le quiz

        // 6. Débloquer la prochaine leçon
        $nextLesson = Lesson::where('course_id', $course->id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();

        if ($nextLesson) {
            LessonUserProgress::updateOrCreate(
                ['lesson_id' => $nextLesson->id, 'user_id' => $userId],
                ['is_locked' => false]
            );
        }

        // 7. Mise à jour de la progression
        $progress->progress_percent = $progressPercent;
        $progress->completed_lessons = $completedLessons;
        $progress->current_lesson_id = $nextLesson?->id;
        $progress->save();

        return response()->json([
            'message' => 'Progression mise à jour avec succès.',
            'completed_lessons' => $completedLessons,
            'quiz_completed' => $quizCompleted,
            'next_unlocked_lesson_id' => $nextLesson?->id,
            'course_progress_percent' => $progressPercent,
        ]);
    }

    public function updateProgressOld(Request $request, $lessonId)
    {
        $userId = auth()->id();

        $lesson = Lesson::with('course')->findOrFail($lessonId);
        $course = $lesson->course;

        // 1. Marquer la leçon comme complétée
        LessonUserProgress::updateOrCreate(
            ['lesson_id' => $lesson->id, 'user_id' => $userId],
            [
                'is_locked' => false,
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        // 2. Récupération ou création de la progression du cours
        $progress = CourseUserProgress::firstOrNew([
            'user_id' => $userId,
            'course_id' => $course->id
        ]);

        // 3. Mettre à jour la liste des leçons complétées
        $completedLessons = LessonUserProgress::where('user_id', $userId)
            ->whereHas('lesson', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();
            // dd($completedLessons);
        // 4. Calcul du pourcentage de progression
        $totalLessons = Lesson::where('course_id', $course->id)->count();
        $progressPercent = $totalLessons > 0
            ? round((count($completedLessons) / $totalLessons) * 100, 2)
            : 0;

        // 5. Débloquer la prochaine leçon
        $nextLesson = Lesson::where('course_id', $course->id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order')
            ->first();

        if ($nextLesson) {
            LessonUserProgress::updateOrCreate(
                ['lesson_id' => $nextLesson->id, 'user_id' => $userId],
                ['is_locked' => false]
            );
        }

        // 6. Mise à jour finale de la progression
        $progress->progress_percent = $progressPercent;
        $progress->completed_lessons = $completedLessons;
        $progress->current_lesson_id = $nextLesson?->id;
        $progress->save();

        return response()->json([
            'message' => 'Progression mise à jour avec succès.',
            'completed_lessons' => $completedLessons,
            'next_unlocked_lesson_id' => $nextLesson?->id,
            'course_progress_percent' => $progressPercent,
        ]);
    }



}
