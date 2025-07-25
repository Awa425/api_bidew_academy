<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseUserProgress;
use Illuminate\Http\Request;


class CourseProgressController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/courses/{courseId}/progress",
     *      summary="Marquer une Leçon terminée",
     *      tags={"Progression"},
     *      security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *      @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              required={"lesson_id"},
     *              @OA\Property(
     *                  property="lesson_id",
     *                  type="integer",
     *                  example=1,
     *                  description="ID du Leçon terminée"
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Leçon terminée",
     *         @OA\JsonContent(ref="#/components/schemas/Progression")
     *     ),
     *     @OA\Response(response=400, description="Données invalides")
     * )
     */
    public function updateProgress(Request $request, $courseId)
    {
        //  Récupérer current user et l'ID de la leçon
        $user = auth()->id();
        $lessonId = $request->input('lesson_id');
        // Vérifier que le cours et la leçon existent et sont liés
        $course = Course::with('lessons')->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);

        // Récupérer ou créer l'enregistrement de progression
        $progress = CourseUserProgress::firstOrNew([
            'user_id' => $user,
            'course_id' => $course->id
        ]);

        // Changer etat is_locked du cours
        $lesson->is_locked = false;
        $lesson->update();
        $lessonSuivant = $course->lessons()->where('order', '>' , $lesson->order)
                                           ->orderBy('order', 'asc')
                                           ->first();
        if ($lessonSuivant) {
            $lessonSuivant->is_locked = false;
            $lessonSuivant->update();                                  
        }                                   

        //  Ajouter la leçon à la liste des leçons complétées
        $completed = $progress->completed_lessons ?? [];
        if (!in_array($lessonId, $completed)) {
            $completed[] = $lessonId;
        }

        // Mettre à jour la progression
        $progress->completed_lessons = $completed;
        $progress->current_lesson_id = $lessonId;
        $progress->progress_percent = count($completed) / $course->lessons()->count() * 100;

        if (!$progress->started_at) $progress->started_at = now();
        if ($progress->progress_percent >= 100 && !$progress->completed_at) {
            $progress->completed_at = now();
        }

        $progress->save();

        return response()->json(['progress' => $progress], 200);
    }

         /**
     * @OA\Get(
     *     path="/api/courses/{courseId}/progress",
     *     summary="Recuperer les progressions",
     *     tags={"Progression"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des progressions",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Progression"))
     *     )
     * )
     */
    public function getProgress($courseId)
    {
        $user = auth()->id();
        $progress = CourseUserProgress::where('user_id', $user)
            ->where('course_id', $courseId)
            ->first();

        return response()->json($progress);
    }

    
}
