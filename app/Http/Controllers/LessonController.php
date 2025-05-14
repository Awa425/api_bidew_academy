<?php

namespace App\Http\Controllers;

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
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'required|integer',
            'duration_minutes' => 'required|integer|min:1',
            'is_published' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $lesson = $course->lessons()->create($request->all());
        return response()->json($lesson, 201);
    }

    /**
     * @OA\Put(
     *      path="/api/lessons/{lesson_id}",
     *      operationId="updateLesson",
     *      tags={"Lessons"},
     *      summary="Update lesson",
     *      description="Update an existing lesson",
     *      security={
     *          {"bearerAuth": {}}
     *      },
     *      @OA\Parameter(
     *          name="lesson_id",
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
     *              @OA\Property(property="title", type="string", format="string", example="Lesson 1: Introduction"),
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
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'order' => 'sometimes|required|integer',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'is_published' => 'sometimes|boolean'
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
