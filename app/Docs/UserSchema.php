<?php
namespace App\Docs;
/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Awa Diop"),
 *     @OA\Property(property="email", type="string", format="email", example="diopawa192@gmail.com"),
 *     @OA\Property(property="password", type="string", example="passer"),
 *     @OA\Property(property="role", type="string", example="admin")
 * )
 */
class UserSchema {}