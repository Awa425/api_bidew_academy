<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class ResourceController extends Controller
{
  
  
public function store(Request $request, Lesson $lesson)
{
    $userId = auth()->id();
    if ($userId !== $lesson->course->user_id) {
        return response()->json(['error' => 'Non autorisé à ajouter une ressource à cette leçon.'], 403);
    }

    $validator = Validator::make($request->all(), [
        'title' => 'required|string',
        'type' => 'required|in:pdf,video,link,text',
        'description' => 'nullable|string',
        'path' => 'nullable|file|mimes:pdf,mp4,avi,mov|max:20480',
        'external_url' => 'nullable|url',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $filePath = null;
    if ($request->hasFile('path')) {
        $filePath = $request->file('path')->store('resources', 'public');
    }

    $resource = $lesson->resources()->create([
        'title' => $request->title,
        'type' => $request->type,
        'description' => $request->description,
        'path' => $filePath,
        'external_url' => $request->external_url,
    ]);

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
