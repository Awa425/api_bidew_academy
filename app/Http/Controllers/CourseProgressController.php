<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseUserProgress;
use Illuminate\Http\Request;


class CourseProgressController extends Controller
{
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
        $lessonSuivant->is_locked = false;
        $lessonSuivant->update();                                  

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

    public function getProgress($courseId)
    {
        $user = auth()->id();
        $progress = CourseUserProgress::where('user_id', $user)
            ->where('course_id', $courseId)
            ->first();

        return response()->json($progress);
    }
}
