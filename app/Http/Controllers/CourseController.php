<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LessonUserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/courses",
     *     summary="Lister tous les cours",
     *     tags={"Courses"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des cours",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Course"))
     *     )
     * )
     */
    public function index()
    { 
        $courses = Course::with(['lessons.contents', 'resources', 'quizzes.questions' ,'evaluations'])
            ->when(request('published_only'), function ($query) {
                $query->where('is_published', true);
            })
            
            ->paginate(10);

        return response()->json($courses);
    }

    /**
     * @OA\Post(
     *     path="/api/courses",
     *     summary="Créer un nouveau cours",
     *     tags={"Courses"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Course")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cours créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Course")
     *     ),
     *     @OA\Response(response=400, description="Données invalides")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'prerequis' => 'nullable|string',
            'objectif' => 'nullable|string',
            'progression' => 'nullable|integer|min:0|max:100',
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

        $data = $request->except('image_path');
        // dd($request);
        if ($request->hasFile('image_path')) {
            $image = $request->file('image_path');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/courses'), $imageName);
            $data['image_path'] = 'uploads/courses/' . $imageName;
        }

        $data['user_id'] = auth()->id();
        
        $course = Course::create($data);

        return response()->json($course, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/courses/{id}",
     *     summary="Get course details",
     *     tags={"Courses"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course data",
     *         @OA\JsonContent(ref="#/components/schemas/Course")
     *     )
     * )
     */
    public function show($course_id)
    {
        $course = Course::find($course_id);
  
        if (is_null($course)) {
            return $this->sendError('Course not found.');
        }
        return response()->json($course->load('user','lessons.contents',  'quizzes.questions' ,'resources'));
        
    }

    /**
     * @OA\Put(
     *     path="/api/courses/{course}",
     *     summary="Modifier ou mettre a jour les inlfos du cours",
     *     tags={"Courses"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Course")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Modifier avec succes",
     *         @OA\JsonContent(ref="#/components/schemas/Course")
     *     ),
     *     @OA\Response(response=404, description="Cours non trougvé")
     * )
     */
    public function update(Request $request, Course $course)
    {
        // Vérification que l'utilisateur authentifié est le créateur du cours
        if (auth()->id() !== $course->user_id) {
            return response()->json(['error' => 'Vous n\'avez pas la permission de modifier ce cours.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'prerequis' => 'nullable|string',
            'objectif' => 'nullable|string',
            'progression' => 'nullable|integer|min:0|max:100',
            'category' => 'nullable|string',
            'level' => 'nullable|string',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_published' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

         $data = $request->except('image_path');
      
        if ($request->hasFile('image_path')) {
            $image = $request->file('image_path');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/courses'), $imageName);
            $data['image_path'] = 'uploads/courses/' . $imageName;
        }

        $course->update($data);

        return response()->json([
            'message' => 'Cours mis à jour avec succès.',
            'course' => $course
        ]);
    }

        /**
     * @OA\Post(
     *     path="/api/courses/{id}/start",
     *     summary="Démarrer cours. Première leçon débloquée.",
     *     tags={"Courses"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cours démarré. Première leçon débloquée.",
     *         @OA\JsonContent(ref="#/components/schemas/Course")
     *     ),
     *     @OA\Response(response=400, description="Données invalides")
     * )
     */
    public function startCourse($courseId)
    {
        $userId = auth()->id();
        $course = Course::findOrFail($courseId);

        $firstLesson = $course->lessons()->orderBy('order')->first();

        if ($firstLesson) {
            LessonUserProgress::firstOrCreate([
                'user_id' => $userId,
                'lesson_id' => $firstLesson->id,
            ], [
                'is_locked' => false,
                'is_completed' => false,
            ]);
        }

        return response()->json(['message' => 'Cours démarré. Première leçon débloquée.']);
    }


    public function destroy(Course $course)
    {
        return response()->json(null, 204);
    }
}
