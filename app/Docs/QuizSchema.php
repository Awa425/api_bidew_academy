<?php
namespace App\Docs;

/**
 * @OA\Schema(
 *     schema="Quiz",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=2),
 *     @OA\Property(property="title", type="string", example="Quiz 1"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="cours_id", type="integer")
 * )
 */
class QuizSchema {}