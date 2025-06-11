<?php
namespace App\Docs;

/**
 * @OA\Schema(
 *     schema="Progression",
 *     type="object",
 *     title="Progression",
 *     description="Progression du cours",
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="Utilisateur associé",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="cours_id",
 *         type="integer",
 *         description="Utilisateur associé",
 *         example="1"
 *     ),
  *     @OA\Property(
 *         property="current_lesson_id",
 *         type="integer",
 *         description="current lesson",
 *         example="1"
 *     ),
  *     @OA\Property(
 *         property="completed_lessons",
 *         type="array",
 *         @OA\Items(
 *             type="integer",
 *             example="1"
 *         ),
 *         description="Lesson terminés"
 *     ),
 *     @OA\Property(
 *         property="progress_percent",
 *         type="integer",
 *         description="Pourcentage",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="started_at",
 *         type="date",
 *         description="Date de debut",
 *        example="2025-01-15"
 *     ),
 * )
 */
class ProgressionSchema {}