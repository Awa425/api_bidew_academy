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

        // Vérifie que la leçon existe
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

        // 2. Débloquer la prochaine leçon (par ordre croissant)
        $nextLesson = Lesson::where('course_id', $course->id)
            ->where('order', '>', $lesson->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextLesson) {
            LessonUserProgress::updateOrCreate(
                ['lesson_id' => $nextLesson->id, 'user_id' => $userId],
                ['is_locked' => false]
            );
        }

        // 3. Mettre à jour la progression globale du cours
        $totalLessons = Lesson::where('course_id', $course->id)->count();

        $completedLessons = LessonUserProgress::where('user_id', $userId)
            ->whereHas('lesson', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->where('is_completed', true)
            ->count();

        $progressPercent = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100, 2)
            : 0;

        CourseUserProgress::updateOrCreate(
            ['user_id' => $userId, 'course_id' => $course->id],
            ['progress_percent' => $progressPercent]
        );

        return response()->json([
            'message' => 'Progression mise à jour avec succès.',
            'completed_lesson_id' => $lesson->id,
            'next_unlocked_lesson_id' => $nextLesson?->id,
            'course_progress_percent' => $progressPercent,
        ]);
    }


}
