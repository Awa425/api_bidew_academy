<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Google_Client;

class SocialAuthController extends Controller
{
    /**
     * Gère la connexion avec Google via un token JWT
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleLogin(Request $request)
    {
        try {
            $request->validate([
                'id_token' => 'required|string',
            ]);
            
            // Vérifier le token Google côté serveur
            $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->id_token);
            

            if (!$payload) {
                Log::error('Invalid Google token');
                return response()->json([
                    'success' => false,
                    'message' => 'Token Google invalide.'
                ], 401);
            }


            // Vérifier si l'email est présent dans le payload
            if (!isset($payload['email'])) {
                Log::error('Email not found in Google payload', ['payload' => $payload]);
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de récupérer l\'email depuis le compte Google.'
                ], 400);
            }

            // Vérifier si l'utilisateur existe déjà
            $user = User::where('email', $payload['email'])->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $payload['name'] ?? $payload['email'],
                    'email' => $payload['email'],
                    'google_id' => $payload['sub'],
                    'password' => Hash::make("passer"),
                    'email_verified_at' => now(),
                    'role' => 'apprenant'
                ]);
            
            } else {
                if (empty($user->google_id)) {
                    $user->google_id = $payload['sub'];
                    $user->save();
                }
            }
            Auth::login($user);
            
            
            $token = $user->createToken('auth-token')->plainTextToken;
            
            $result = response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'token' => $token,
                'message' => 'Connexion réussie avec Google.'
            ]);
            return $result;

        } catch (\Exception $e) {
            Log::error('Google authentication error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la connexion avec Google.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
