<?php
namespace App\Docs;
/**
 * @OA\Schema(
 *     schema="Course",
 *     type="object",
 *     title="Course",
 *     required={"id", "title", "description", "progression", "category", "level", "duration_minutes", "is_published", "user"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Initiation de base a l'informatique"),
 *     @OA\Property(property="description", type="string", example="Learn the basics of Informatique"),
 *     @OA\Property(property="prerequis", type="string", nullable=true),
 *     @OA\Property(property="objectif", type="string", nullable=true),
 *     @OA\Property(property="progression", type="number", format="float", example=0),
 *     @OA\Property(property="category", type="string", example="Word"),
 *     @OA\Property(property="level", type="string", example="Beginner"),
 *     @OA\Property(property="image_path", type="string", nullable=true),
 *     @OA\Property(property="duration_minutes", type="integer", example=160),
 *     @OA\Property(property="is_published", type="boolean", example=true),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="lessons", type="array", @OA\Items(ref="#/components/schemas/Lesson")),
 *     @OA\Property(property="resources", type="array", @OA\Items(type="object")), 
 *     @OA\Property(property="evaluations", type="array", @OA\Items(type="object"))
 * )
 */
class CoursSchema {}