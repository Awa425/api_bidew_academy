<?php
namespace App\Docs;

/**
 * @OA\Schema(
 *     schema="Answer",
 *     type="object",
 *     @OA\Property(property="text", type="string", example="Contenu reponse"),
 *     @OA\Property(property="is_correct", type="boolean", nullable=true,  example="true"),
 * )
 */
class AnswerSchema {}