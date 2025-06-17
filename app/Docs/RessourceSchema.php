<?php
namespace App\Docs;
/**
 * @OA\Schema(
 *     schema="Ressource",
 *     type="object",
 *     required={"id", "type"},
 *     @OA\Property(property="id", type="integer", example=2),
 *     @OA\Property(property="title", type="string", example="Livre Laravel"),
 *     @OA\Property(property="type", type="string", example="pdf"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="path", type="string", example="lessons/files/example.pdf"),
 *     @OA\Property(property="external_url", type="string", nullable=true)
 * )
 */
class RessourceSchema {}