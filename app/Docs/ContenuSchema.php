<?php
namespace App\Docs;
/**
 * @OA\Schema(
 *     schema="Contenu",
 *     type="object",
 *     title="Contenu",
 *     description="Contenu",
 *     @OA\Property(
 *         property="Introduction",
 *         type="string",
 *         description="Titre du Contenu",
 *         example="Contenu 1"
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
 * )
 */
class ContenuSchema {}