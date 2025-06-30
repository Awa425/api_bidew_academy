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

    /**
     * @OA\Get(
     *     path="/api/courses/{course}/lessons",
     *     summary="Lister tous les lessons",
     *     tags={"Leçon"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des Lessosn",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Lesson"))
     *     )
     * )
     */
    public function index($course)
    { 
        $lessons = Lesson::with(['course','contents','resources'])
            ->where('course_id', $course)
            ->orderBy('id','desc')
            ->paginate(10);

        return response()->json($lessons);
    }
    /**
     * @OA\Get(
     *     path="/api/courses/{course}/lessons/{lesson}",
     *     summary="Detail lesson",
     *     tags={"Leçon"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course data",
     *         @OA\JsonContent(ref="#/components/schemas/Lesson")
     *     )
     * )
     */
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


    /**
     * @OA\Post(
     *      path="/api/courses/{course}/lessons",
     *      summary="Création de nouvelle leçon",
     *      tags={"Leçon"},
     *      security={{"sanctumAuth":{}}},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Lesson")
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Leçon créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Lesson")
     *     ),
     *     @OA\Response(response=400, description="Données invalides")
     * )
     */
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
            'duration_minutes' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
            'content.type' => 'required|in:text,video,pdf,link,jpg,jpeg',
            'content.data' => 'nullable|string',
            'content.external_url' => 'nullable|url',
            'content.file' => 'nullable|file|mimes:pdf,mp4,avi,mov,jpg,jpeg|max:20480' // max 20MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nextOrder = ($course->lessons()->max('order') ?? 0) + 1;

        // Débloquer uniquement la première leçon automatiquement
        $isLocked = $nextOrder > 1 ? true : false;

        // Création de la leçon
        $lesson = $course->lessons()->create([
            'title' => $request->title,
            'order' => $nextOrder,
            'duration_minutes' => $request->duration_minutes,
            'is_published' => $request->is_published ?? false,
            'user_id' => $userId,
            'is_locked' => $isLocked,
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


    /**
     * @OA\Put(
     *     path="/api/lessons/{lesson}",
     *     summary="Modifier ou mettre a jour les inlfos d'une Leçon",
     *     tags={"Leçon"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="lesson",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Lesson")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Modifier avec succes",
     *         @OA\JsonContent(ref="#/components/schemas/Lesson")
     *     ),
     *     @OA\Response(response=404, description="Lesson non trougvé")
     * )
     */

    public function update(Request $request, Course $course,Lesson $lesson)
    {
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
        'title' => 'sometimes|required|string',
        'duration_minutes' => 'nullable|integer|min:1',
        'is_published' => 'boolean',
        'is_locked' => 'boolean',
        'content.type' => 'sometimes|required|string|in:text,video,pdf,link,jpg,jpeg',
        'content.data' => 'nullable|string',
        'content.external_url' => 'nullable|url',
        'content.file' => 'nullable|file|mimes:pdf,mp4,avi,mov|max:20480,jpg,jpeg'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Mise à jour des données de la leçon
    $lesson->update([
        'title' => $request->input('title', $lesson->title),
        'duration_minutes' => $request->input('duration_minutes', $lesson->duration_minutes),
        'is_published' => $request->input('is_published', $lesson->is_published),
        'is_locked' => $request->input('is_locked', $lesson->is_locked),
    ]);

    // Traitement du fichier si fourni
    $filePath = null;
    if ($request->hasFile('content.file')) {
        $file = $request->file('content.file');
        $filePath = $file->store('lessons/files', 'public');
    }

    // Mise à jour du contenu si fourni
    if ($request->has('content')) {
        $contentData = [];

        if ($request->filled('content.type')) {
            $contentData['type'] = $request->input('content.type');
        }

        if ($request->has('content.data')) {
            $contentData['data'] = $request->input('content.data');
        }

        if ($request->has('content.external_url')) {
            $contentData['external_url'] = $request->input('content.external_url');
        }

        if ($request->hasFile('content.file')) {
            $file = $request->file('content.file');
            $filePath = $file->store('lessons/files', 'public');
            $contentData['file_path'] = $filePath;
        }

        // S'il y a des données de contenu à mettre à jour
        if (!empty($contentData)) {
            $existingContent = $lesson->contents()->first();

            if ($existingContent) {
                $existingContent->update($contentData);
            } else {
                // On ne crée que si le type est présent (obligatoire pour créer)
                if (!empty($contentData['type'])) {
                    $lesson->contents()->create($contentData);
                }
            }
        }
    }

    return response()->json([
        'lesson' => $lesson->load('contents'),
    ], 200);
    }


   /**
     * @OA\Delete(
     *     path="/api/lessons/{lesson}",
     *     summary="Supprimer une Leçon",
     *     tags={"Leçon"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="lesson",
     *         in="path",
     *         description="ID de la Leçon à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Leçon supprimé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Leçon non trouvé")
     * )
     */
       public function destroy(Lesson $lesson)
    {
        $userId = auth()->id();
        $courseUserId = $lesson->course->user_id;
        if ($userId !== $courseUserId) {
            return response()->json([
                'error' => 'Vous n\'avez pas la permission de supprimer cette leçon.',
                'debug' => [
                    'current_user_id' => $userId,
                    'course_user_id' => $courseUserId
                    ]
                ], 403);
        }
        $lesson->delete();
        return response()->json(null, 204);
    }
}
