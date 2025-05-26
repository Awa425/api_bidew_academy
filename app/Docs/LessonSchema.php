<?php
namespace App\Docs;
/**
 * @OA\Schema(
 *     schema="Lesson",
 *     type="object",
 *     title="Lesson",
 *     description="lesson",
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Titre du lesson",
 *         example="lesson 1"
 *     ),
 *     @OA\Property(
 *         property="order",
 *         type="integer",
 *         description="Niveau",
 *         example="1"
 *     ),
  *     @OA\Property(
 *         property="duration_minutes",
 *         type="integer",
 *         description="durée",
 *         example="160"
 *     ),
 *     @OA\Property(
 *         property="Content",
 *         ref="#/components/schemas/Content",
 *         description="Contenue associé"
 *     ),
 *     @OA\Property(
 *         property="duration_minutes",
 *         type="string", 
 *         description="duration minutes",
 *         example="24h"
 *     )
 * )
 */
class LessonSchema {}