<?php
namespace App\Docs;

/**
 * @OA\Schema(
 *     schema="Quiz",
 *     type="object",
 *     @OA\Property(property="title", type="string", example="Quiz 1"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="questions", type="array", @OA\Items(ref="#/components/schemas/Question"))
 * )
 */
class QuizSchema {}