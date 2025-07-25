<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class ResourceController extends Controller
{
      /**
     * @OA\Get(
     *     path="/api/lesson/{lesson}/resources",
     *     summary="Lister tous les ressources d'une Leçon",
     *     tags={"Ressources"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="lesson",
     *         in="path",
     *         description="ID de la Leçon",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des ressources",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Ressource"))
     *     )
     * )
     */
    public function index($lesson){
        $ressources = Resource::where('lesson_id', $lesson)->get();
          
        return response()->json($ressources);

    }
      /**
     * @OA\Post(
     *      path="/api/lesson/{lesson}/resources",
     *      summary="Création de nouvelle ressource pour une Leçon",
     *      tags={"Ressources"},
     *      security={{"sanctumAuth":{}}},
     *      @OA\Parameter(
     *         name="lesson",
     *         in="path",
     *         description="ID de la Leçon",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Ressource")
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Leçon créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Ressource")
     *     ),
     *     @OA\Response(response=400, description="Données invalides")
     * )
     */
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
            'path' => 'nullable|file|mimes:pdf,mp4,avi,mov',
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


    /**
     * @OA\Put(
     *     path="/api/lesson/{lesson}/resources/{resource}",
     *     summary="Modifier ou mettre a jour les inlfos d'une Leçon",
     *     tags={"Ressources"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="lesson",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="resource",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Ressource")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Modifier avec succes",
     *         @OA\JsonContent(ref="#/components/schemas/Ressource")
     *     ),
     *     @OA\Response(response=404, description="Ressource non trougvé")
     * )
     */
    public function update(Request $request, Lesson $lesson, Resource $resource)
    {
        $userId = auth()->id();

        // Vérifie si l'utilisateur est autorisé à modifier cette ressource
        if ($userId !== $lesson->course->user_id) {
            return response()->json(['error' => 'Non autorisé à modifier une ressource de cette leçon.'], 403);
        }

        // Vérifie que la ressource appartient bien à la leçon
        if ($resource->lesson_id !== $lesson->id) {
            return response()->json(['error' => 'Cette ressource ne correspond pas à la leçon spécifiée.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string',
            'type' => 'sometimes|in:pdf,video,link,text',
            'description' => 'nullable|string',
            'path' => 'nullable|file|mimes:pdf,mp4,avi,mov,jpg,jpeg,mkv',
            // 'external_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('path')) {
            $filePath = $request->file('path')->store('resources', 'public');
            $resource->path = $filePath;
        }

        $resource->title = $request->get('title', $resource->title);
        $resource->type = $request->get('type', $resource->type);
        $resource->description = $request->get('description', $resource->description);
        // $resource->external_url = $request->get('external_url', $resource->external_url);

        $resource->save();

        return response()->json($resource);
    }


    /**
     * @OA\Delete(
     *     path="/api/resources/{resource}",
     *     summary="Supprimer une ressource",
     *     tags={"Ressources"},
     *     security={{"sanctumAuth":{}}},
     *     @OA\Parameter(
     *         name="resource",
     *         in="path",
     *         description="ID de la ressource à supprimer",
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
     public function destroy(Resource $resource)
    {
        $resource->delete();
        return response()->json(null, 204);
    }
}
