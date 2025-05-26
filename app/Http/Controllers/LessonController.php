<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class LessonController extends Controller
{


    public function index($course)
    { 
        $lessons = Lesson::with(['course','contents','resources'])
            ->where('course_id', $course)
            ->orderBy('id','desc')
            ->paginate(10);

        return response()->json($lessons);
    }

    public function show(Course $course, Lesson $lesson)
    {
        $user = auth()->user();

        if ($lesson->is_locked) {
            // Vérifie si l'utilisateur a terminé les leçons précédentes
            $previousLessons = $course->lessons()
                ->where('order', '<', $lesson->order)
                ->pluck('id');

            $completed = $user->completedLessons()->whereIn('lesson_id', $previousLessons)->count();

            if ($completed < $previousLessons->count()) {
                return response()->json([
                    'error' => 'Cette leçon est verrouillée. Vous devez terminer les leçons précédentes.'
                ], 403);
            }
        }

        return response()->json($lesson->load('contents'));
    }


   
public function store(Request $request, Course $course)
{
    $userId = auth()->id();
    if ($userId !== $course->user_id) {
        return response()->json([
            'error' => 'Vous n\'avez pas la permission de créer une leçon pour ce cours.',
        ], 403);
    }

    // Validation
    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
        'order' => 'nullable|integer',
        'duration_minutes' => 'nullable|integer|min:1',
        'is_published' => 'boolean',
        'is_locked' => 'boolean',
        'content.type' => 'required|in:text,video,pdf,link',
        'content.data' => 'nullable|string',
        'content.external_url' => 'nullable|url',
        'content.file' => 'nullable|file|mimes:pdf,mp4,avi,mov|max:20480' // max 20MB
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Création de la leçon
    $lesson = $course->lessons()->create([
        'title' => $request->title,
        'order' => $request->order,
        'duration_minutes' => $request->duration_minutes,
        'is_published' => $request->is_published ?? false,
        'user_id' => $userId,
        'is_locked' => $request->input('is_locked', false),
    ]);

    // Traitement du fichier s'il est fourni
    $filePath = null;
    if ($request->hasFile('content.file')) {
        $file = $request->file('content.file');
        $filePath = $file->store('lessons/files', 'public');
    }

    // Création du contenu lié
    $lesson->contents()->create([
        'type' => $request->input('content.type'),
        'data' => $request->input('content.data'),
        'file_path' => $filePath,
        'external_url' => $request->input('content.external_url'),
    ]);

    return response()->json([
        'lesson' => $lesson->load('contents'),
    ], 201);
}


   
     public function update(Request $request, Lesson $lesson)
    {
        // Vérifier si l'utilisateur peut mettre à jour la leçon
        $userId = auth()->id();
        $courseUserId = $lesson->course->user_id;
        if ($userId !== $courseUserId) {
            return response()->json([
                'error' => 'Vous n\'avez pas la permission de modifier cette leçon.',
                'debug' => [
                    'current_user_id' => $userId,
                    'course_user_id' => $courseUserId
                ]
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'order' => 'sometimes|required|integer',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'is_published' => 'sometimes|boolean',
            'is_locked' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $lesson->update($request->all());
        return response()->json($lesson);
    }

  
       public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return response()->json(null, 204);
    }
}
