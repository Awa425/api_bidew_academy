<?php
namespace App\Docs;
/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="Schéma pour un utilisateur",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nom complet de l'utilisateur",
 *         example="Awa Diop"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email de l'utilisateur",
 *         example="admin@bidew.com"
 *     ),
  *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Mot de passe de l'utilisateur",
 *         example="password"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string", 
 *         description="Role associée",
 *         example="formateur"
 *     )
 * )
 */
class UserSchema {}