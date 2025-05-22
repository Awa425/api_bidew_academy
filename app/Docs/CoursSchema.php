<?php
namespace App\Docs;
/**
 * @OA\Schema(
 *     schema="Cours",
 *     type="object",
 *     title="Cours",
 *     description="Les Cours",
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Titre du cours",
 *         example="Cours de Securité"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="description du cours",
 *         example="Ce cours parle sur la securité..."
 *     ),
  *     @OA\Property(
 *         property="category",
 *         type="string",
 *         description="Category du cours",
 *         example="category qu appartient a ce cours"
 *     ),
 *     @OA\Property(
 *         property="level",
 *         type="string", 
 *         description="Niveau du cours",
 *         example="Debutant"
 *     ),
 *     @OA\Property(
 *         property="image_path",
 *         type="string", 
 *         description="Image associée",
 *         example="path_image"
 *     ),
 *     @OA\Property(
 *         property="duration_minutes",
 *         type="string", 
 *         description="duration minutes",
 *         example="24h"
 *     )
 * )
 */
class CoursSchema {}