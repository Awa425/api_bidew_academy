<?php
namespace App\Docs;
/**
 * @OA\Schema(
 *     schema="Lesson",
 *     type="object",
 *     required={"id", "title", "order", "duration_minutes"},
 *     @OA\Property(property="id", type="integer", example=2),
 *     @OA\Property(property="title", type="string", example="Introduction à Laravel"),
 *     @OA\Property(property="course_id", type="integer", example=1),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="duration_minutes", type="integer", example=60),
 *     @OA\Property(property="is_published", type="boolean", example=false),
 *     @OA\Property(property="is_locked", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="contents", type="array", @OA\Items(ref="#/components/schemas/Content"))
 * )
 */
class LessonSchema {}