<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
/**
 * @OA\Post(
 *      path="/api/register",
 *      operationId="createUser",
 *      tags={"Inscription"},
 *      summary="S'inscripre",
 *      description="Inscription d'un nouvel utilisateur", 
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(ref="#/components/schemas/User")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Succès",
 *          @OA\JsonContent(ref="#/components/schemas/User")
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Erreur de validation"
 *      )
 * )
 */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'in:admin,formateur,apprenant'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'apprenant',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

   /**
 * @OA\Post(
 *      path="/api/login",
 *      operationId="login",
 *      tags={"login"},
 *      summary="Se connecter",
 *      description="Permet a l'utilisateur de se connecter avec son email et un mot de passe.", 
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              type="object",
 *              required={"email", "password"},
 *              @OA\Property(
 *                  property="email",
 *                  type="string",
 *                  example="diopawa425@gmail.com",
 *                  description="Email votre email"
 *              ),
 *              @OA\Property(
 *                  property="password",
 *                  type="string",
 *                  example="passer"
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Connexion réussie",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="token", type="string"),
 *              @OA\Property(property="data", type="object")
 *          )
 *      ),
 *      @OA\Response( 
 *          response=401,
 *          description="Email ou mot de passe incorrects"
 *      ),
 *      @OA\Response( 
 *          response=422,
 *          description="Erreur de validation"
 *      )
 * )
 */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }
}
