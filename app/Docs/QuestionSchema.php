<?php
namespace App\Docs;

/**
 * @OA\Schema(
 *     schema="Question",
 *     type="object",
 *     @OA\Property(property="type", type="string", nullable=true,  example="multiple_choice"),
 *     @OA\Property(property="text", type="string", example="Contenu quiz"),
 *     @OA\Property(property="question", type="array", @OA\Items(ref="#/components/schemas/Answer"))
 * )
 */
class QuestionSchema {}