<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="E-Learning API Documentation",
 *      description="API Documentation for E-Learning Platform",
 *      @OA\Contact(
 *          email="contact@example.com"
 *      )
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 *      schema="Course",
 *      type="object",
 *      @OA\Property(property="id", type="integer", readOnly=true),
 *      @OA\Property(property="title", type="string"),
 *      @OA\Property(property="description", type="string"),
 *      @OA\Property(property="category", type="string"),
 *      @OA\Property(property="level", type="string"),
 *      @OA\Property(property="image_path", type="string"),
 *      @OA\Property(property="duration_minutes", type="integer"),
 *      @OA\Property(property="is_published", type="boolean"),
 *      @OA\Property(property="user_id", type="integer"),
 *      @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * )
 *
 * @OA\Schema(
 *      schema="Lesson",
 *      type="object",
 *      @OA\Property(property="id", type="integer", readOnly=true),
 *      @OA\Property(property="course_id", type="integer"),
 *      @OA\Property(property="title", type="string"),
 *      @OA\Property(property="content", type="string"),
 *      @OA\Property(property="order", type="integer"),
 *      @OA\Property(property="duration_minutes", type="integer"),
 *      @OA\Property(property="is_published", type="boolean"),
 *      @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * )
 *
 * @OA\Schema(
 *      schema="Resource",
 *      type="object",
 *      @OA\Property(property="id", type="integer", readOnly=true),
 *      @OA\Property(property="course_id", type="integer"),
 *      @OA\Property(property="type", type="string", enum={"link", "software", "guide"}),
 *      @OA\Property(property="title", type="string"),
 *      @OA\Property(property="url", type="string"),
 *      @OA\Property(property="description", type="string"),
 *      @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * )
 *
 * @OA\Schema(
 *      schema="Evaluation",
 *      type="object",
 *      @OA\Property(property="id", type="integer", readOnly=true),
 *      @OA\Property(property="course_id", type="integer"),
 *      @OA\Property(property="type", type="string", enum={"quiz", "qcm", "essay"}),
 *      @OA\Property(property="title", type="string"),
 *      @OA\Property(property="description", type="string"),
 *      @OA\Property(
 *          property="questions",
 *          type="array",
 *          @OA\Items(
 *              @OA\Property(property="question", type="string"),
 *              @OA\Property(property="type", type="string", enum={"mcq", "tf", "essay"}),
 *              @OA\Property(
 *                  property="options",
 *                  type="array",
 *                  @OA\Items(type="string")
 *              ),
 *              @OA\Property(property="correct_answer", type="string"),
 *              @OA\Property(property="points", type="integer")
 *          )
 *      ),
 *      @OA\Property(property="total_points", type="integer"),
 *      @OA\Property(property="passing_score", type="integer"),
 *      @OA\Property(property="time_limit_minutes", type="integer"),
 *      @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * )
 */

class CourseController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/courses",
     *      operationId="getCoursesList",
     *      tags={"Courses"},
     *      summary="Get list of courses",
     *      description="Returns list of courses",
     *      @OA\Parameter(
     *          name="published_only",
     *          description="Filter published courses only",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="boolean"
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
     *                      @OA\Items(ref="#/components/schemas/Course")
     *                  ),
     *                  @OA\Property(
     *                      property="links",
     *                      type="object"
     *                  ),
     *                  @OA\Property(
     *                      property="meta",
     *                      type="object"
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index()
    { 
        $courses = Course::with(['lessons', 'resources', 'evaluations'])
            ->when(request('published_only'), function ($query) {
                $query->where('is_published', true);
            })
            ->paginate(10);

        return response()->json($courses);
    }

    /**
     * @OA\Post(
     *      path="/api/courses",
     *      operationId="createCourse",
     *      tags={"Courses"},
     *      summary="Create new course",
     *      description="Create a new course",
     *      security={
     *          {"sanctumAuth": {}}
     *      },
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title"},
     *              @OA\Property(property="title", type="string", format="string", example="Introduction to Programming"),
     *              @OA\Property(property="description", type="string", format="string", example="Learn the basics of programming"),
     *              @OA\Property(property="category", type="string", format="string", example="Programming"),
     *              @OA\Property(property="level", type="string", format="string", example="Beginner"),
     *              @OA\Property(property="image_path", type="string", format="string", example="path/to/image.jpg"),
     *              @OA\Property(property="duration_minutes", type="integer", format="int32", example="120"),
     *              @OA\Property(property="is_published", type="boolean", format="boolean", example="true")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Course created successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Course"
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'level' => 'nullable|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_published' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!auth()->check()) {
            return response()->json(['error' => 'User must be authenticated'], 401);
        }

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $course = Course::create($data);

        return response()->json($course, 201);
    }

    /**
     * Display the specified course.
     */
    /**
     * @OA\Get(
     *      path="/api/courses/{id}",
     *      operationId="getCourse",
     *      tags={"Courses"},
     *      summary="Get course details",
     *      description="Get detailed information about a course",
     *      @OA\Parameter(
     *          name="id",
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
     *                      ref="#/components/schemas/Course"
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function show($course_id)
    {
        $course = Course::find($course_id);
  
        if (is_null($course)) {
            return $this->sendError('Course not found.');
        }
        return response()->json($course->load('user','lessons', 'resources', 'evaluations'));
        
    }

    /**
     * Update the specified course.
     */
    /**
     * @OA\Put(
     *      path="/api/courses/{id}",
     *      operationId="updateCourse",
     *      tags={"Courses"},
     *      summary="Update course",
     *      description="Update an existing course",
     *      security={
     *          {"bearerAuth": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
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
     *              @OA\Property(property="title", type="string", format="string", example="Updated Course Title"),
     *              @OA\Property(property="description", type="string", format="string", example="Updated course description"),
     *              @OA\Property(property="category", type="string", format="string", example="Programming"),
     *              @OA\Property(property="level", type="string", format="string", example="Intermediate"),
     *              @OA\Property(property="image_path", type="string", format="string", example="path/to/updated/image.jpg"),
     *              @OA\Property(property="duration_minutes", type="integer", format="int32", example="120"),
     *              @OA\Property(property="is_published", type="boolean", format="boolean", example="true")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Course updated successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Course"
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
    public function update(Request $request, Course $course)
    { 
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'category' => 'sometimes|required|string',
            'level' => 'sometimes|required|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'is_published' => 'sometimes|boolean'
        ]);

        if ($validator) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $course->update($request->all());

        return response()->json($course);
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        return response()->json(null, 204);
    }
}
