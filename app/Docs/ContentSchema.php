<?php
namespace App\Docs;
/**
 * @OA\Schema(
 *     schema="Content",
 *     type="object",
 *     required={"id", "type"},
 *     @OA\Property(property="id", type="integer", example=2),
 *     @OA\Property(property="lesson_id", type="integer", example=2),
 *     @OA\Property(property="type", type="string", example="pdf"),
 *     @OA\Property(property="data", type="string", nullable=true),
 *     @OA\Property(property="file_path", type="string", example="lessons/files/example.pdf"),
 *     @OA\Property(property="external_url", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ContentSchema {}