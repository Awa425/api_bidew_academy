<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *      name="Resources",
 *      description="API Endpoints of Resources"
 * )
 */

class ResourceController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/courses/{course_id}/resources",
     *      operationId="createResource",
     *      tags={"Resources"},
     *      summary="Create new resource",
     *      description="Create a new resource for a specific course",
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
     *              required={"type", "title", "url"},
     *              @OA\Property(property="type", type="string", enum={"link", "software", "guide"}, example="link"),
     *              @OA\Property(property="title", type="string", format="string", example="Additional Reading Material"),
     *              @OA\Property(property="url", type="string", format="url", example="https://example.com/resource"),
     *              @OA\Property(property="description", type="string", format="string", example="Supplementary reading material for the course")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Resource created successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Resource"
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
     * @OA\Post(
     *      path="/api/courses/{course_id}/resources",
     *      operationId="createResource",
     *      tags={"Resources"},
     *      summary="Create new resource",
     *      description="Create a new resource for a specific course",
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
     *              required={"type", "title", "url", "description"},
     *              @OA\Property(property="type", type="string", enum={"link", "software", "guide"}, example="link"),
     *              @OA\Property(property="title", type="string", format="string", example="Useful Resource"),
     *              @OA\Property(property="url", type="string", format="uri", example="https://example.com/resource"),
     *              @OA\Property(property="description", type="string", format="string", example="Description of the resource")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Resource created successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Resource"
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
            'type' => 'required|in:link,software,guide',
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $resource = $course->resources()->create($request->all());
        return response()->json($resource, 201);
    }

    /**
     * Update a resource.
     */
    /**
     * @OA\Put(
     *      path="/api/resources/{id}",
     *      operationId="updateResource",
     *      tags={"Resources"},
     *      summary="Update resource",
     *      description="Update an existing resource",
     *      security={
     *          {"bearerAuth": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          description="Resource ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="type", type="string", enum={"link", "software", "guide"}, example="link"),
     *              @OA\Property(property="title", type="string", format="string", example="Updated Resource"),
     *              @OA\Property(property="url", type="string", format="uri", example="https://example.com/updated-resource"),
     *              @OA\Property(property="description", type="string", format="string", example="Updated resource description")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Resource updated successfully",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Resource"
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
    public function update(Request $request, Resource $resource)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|required|in:link,software,guide',
            'title' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|url',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $resource->update($request->all());
        return response()->json($resource);
    }

    /**
     * Delete a resource.
     */
    /**
     * @OA\Delete(
     *      path="/api/resources/{id}",
     *      operationId="deleteResource",
     *      tags={"Resources"},
     *      summary="Delete resource",
     *      description="Delete an existing resource",
     *      security={
     *          {"bearerAuth": {}}
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          description="Resource ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Resource deleted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Resource not found")
     *          )
     *      )
     * )
     */
    public function destroy(Resource $resource)
    {
        $resource->delete();
        return response()->json(null, 204);
    }
}
