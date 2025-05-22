<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class ResourceController extends Controller
{
  
  
    public function store(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'type' => 'required|in:pdf,video,link',
            'path' => 'nullable|string',
            'description' => 'nullable|string',
            'is_downloadable' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $resource = $lesson->resources()->create($validator->validated());
        return response()->json($resource, 201);
    }

 
    public function update(Request $request, Resource $resource)
    {
          // Vérification de l'autorisation
    $userId = auth()->id();
    if ($userId !== $resource->lesson->course->user_id) {
        return response()->json([
            'error' => 'Vous n\'avez pas la permission de créer une leçon pour ce cours.',
        ], 403);
    }

        $data = $request->validate([
            'title' => 'sometimes|required|string',
            'type' => 'sometimes|required|in:pdf,video,link',
            'path' => 'nullable|string',
            'description' => 'nullable|string',
            'is_downloadable' => 'boolean',
        ]);

        $resource->update($data);
        return response()->json($resource);
    }

 
     public function destroy(Resource $resource)
    {
        $resource->delete();
        return response()->json(null, 204);
    }
}
