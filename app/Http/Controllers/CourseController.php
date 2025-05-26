<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
   
    public function index()
    { 
        $courses = Course::with(['lessons.contents', 'resources', 'evaluations'])
            ->when(request('published_only'), function ($query) {
                $query->where('is_published', true);
            })
            
            ->paginate(10);

        return response()->json($courses);
    }

 /**
 * @OA\Post(
 *      path="/api/courses",
 *      operationId="createCours",
 *      tags={"Cours"},
 *      summary="Ajouter un cours",
 *      description="Ajouter un cours",
 *      security={{"bearerAuth":{}}},  
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(ref="#/components/schemas/Cours")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Succès",
 *          @OA\JsonContent(ref="#/components/schemas/Cours")
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Erreur de validation"
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

    $data = $request->except('image_path'); // on exclut d’abord l’image

    // Enregistrement de l’image dans public/uploads/courses
    if ($request->hasFile('image_path')) {
        $image = $request->file('image_path');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('uploads/courses'), $imageName);
        $data['image_path'] = 'uploads/courses/' . $imageName; // stocke le chemin relatif
    }

    $data['user_id'] = auth()->id();
    $course = Course::create($data);

    return response()->json($course, 201);
}




  
    public function show($course_id)
    {
        $course = Course::find($course_id);
  
        if (is_null($course)) {
            return $this->sendError('Course not found.');
        }
        return response()->json($course->load('user','lessons', 'resources', 'evaluations'));
        
    }

  
    public function update(Request $request, $course)
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

        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $course = Course::find($course);

        $course->update($request->all());

        return response()->json($course);
    }

    public function destroy(Course $course)
    {
        return response()->json(null, 204);
    }
}
