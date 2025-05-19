<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *      name="Lessons",
 *      description="API Endpoints of Lessons"
 * )
 */

class LessonController extends Controller
{

    public function index($course)
    { 
        $lessons = Lesson::with(['course'])
            ->where('course_id', $course)
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


    /**
     * @OA\Post(
     *      path="/api/courses/{course_id}/lessons",
     *      operationId="createLesson",
     *      tags={"Lessons"},
     *      summary="Create new lesson",
     *      description="Create a new lesson for a specific course",
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
     *              required={"title", "content", "order", "duration_minutes"},
     *              @OA\Property(property="title", type="string", format="string", example="Lesson 1: Introduction"),
     *              @OA\Property(property="content", type="string", format="string", example="<p>Lesson content here...</p>"),
     *              @OA\Property(property="order", type="integer", format="int32", example="1"),
     *              @OA\Property(property="duration_minutes", type="integer", format="int32", example="30"),
     *              @OA\Property(property="is_published", type="boolean", format="boolean", example="true")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Lesson created successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Lesson"
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
    // Vérification de l'autorisation
    $userId = auth()->id();
    if ($userId !== $course->user_id) {
        return response()->json([
            'error' => 'Vous n\'avez pas la permission de créer une leçon pour ce cours.',
        ], 403);
    }

    // Validation de la leçon
    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
        'order' => 'nullable|integer',
        'duration_minutes' => 'nullable|integer|min:1',
        'is_published' => 'boolean',
        'is_locked' => 'boolean',
        
        // Validation du contenu associé
        'content.type' => 'required|in:text,video,pdf,link',
        'content.data' => 'nullable|string',
        'content.file_path' => 'nullable|string',
        'content.external_url' => 'nullable|url',
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

    // Création du contenu lié
    if ($request->has('content')) {
        $lesson->contents()->create([
            'type' => $request->input('content.type'),
            'data' => $request->input('content.data'),
            'file_path' => $request->input('content.file_path'),
            'external_url' => $request->input('content.external_url'),
        ]);
    }

    return response()->json([
        'lesson' => $lesson->load('contents'), // retourne la leçon avec les contenus liés
    ], 201);
}

    /**
     * @OA\Put(
     *      path="/api/lessons/{id}",
     *      operationId="updateLesson",
     *      tags={"Lessons"},
     *      summary="Update lesson",
     *      description="Update an existing lesson",
     *      security={
     *          {"bearerAuth": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          description="Lesson ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string", format="string", example="Updated Lesson Title"),
     *              @OA\Property(property="content", type="string", format="string", example="<p>Updated lesson content...</p>"),
     *              @OA\Property(property="order", type="integer", format="int32", example="1"),
     *              @OA\Property(property="duration_minutes", type="integer", format="int32", example="30"),
     *              @OA\Property(property="is_published", type="boolean", format="boolean", example="true")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Lesson updated successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Lesson"
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
            'content' => 'sometimes|required|string',
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

    /**
     * Delete a lesson.
     */
    /**
     * @OA\Delete(
     *      path="/api/lessons/{id}",
     *      operationId="deleteLesson",
     *      tags={"Lessons"},
     *      summary="Delete lesson",
     *      description="Delete an existing lesson",
     *      security={
     *          {"bearerAuth": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          description="Lesson ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Lesson deleted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Lesson deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Lesson not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Lesson not found")
     *          )
     *      )
     * )
     */
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return response()->json(null, 204);
    }
}
