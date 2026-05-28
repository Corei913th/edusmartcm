<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Contrôleur d'authentification pour les tests API
 * 
 * @group Authentication
 * 
 * Gère l'authentification pour les tests Postman.
 */
class AuthController extends Controller
{
    /**
     * Connexion et génération de token Sanctum
     *
     * @param Request $request Requête avec email et password
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Utilisateur::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return api_success([
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
        ], 'Connexion réussie');
    }

    /**
     * Déconnexion et révocation du token
     *
     * @param Request $request Requête authentifiée
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return api_success(null, 'Déconnexion réussie');
    }

    /**
     * Informations de l'utilisateur connecté
     *
     * @param Request $request Requête authentifiée
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return api_success([
            'id' => $request->user()->id,
            'nom' => $request->user()->nom,
            'prenom' => $request->user()->prenom,
            'email' => $request->user()->email,
            'role' => $request->user()->role,
        ], 'Informations utilisateur récupérées');
    }
}