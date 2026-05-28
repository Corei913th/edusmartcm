<?php

namespace App\Http\Controllers\Api\V1;

use App\Constants\TokenConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Authentification
 *
 * @authenticated
 */
class AuthController extends Controller {
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * Connexion utilisateur.
     *
     * @unauthenticated
     *
     * @bodyParam email string required
     * @bodyParam password string required
     */
    public function login(LoginRequest $request): JsonResponse {
        $result = $this->authService->login(
            $request->email,
            $request->password,
        );

        return api_success([
            'access_token' => $result->token,
            'token_type' => TokenConstants::TOKEN_TYPE,
            'expires_in' => TokenConstants::DEFAULT_ACCESS_TOKEN_EXPIRY_MINUTES * 60,
            'user' => new AuthResource($result->user),
        ], 'Connexion réussie');
    }

    /**
     * Inscription utilisateur.
     *
     * @unauthenticated
     *
     * @bodyParam nom string required
     * @bodyParam prenom string required
     * @bodyParam email string required
     * @bodyParam password string required
     * @bodyParam telephone string optional
     * @bodyParam role_code string optional
     */
    public function register(RegisterRequest $request): JsonResponse {
        $result = $this->authService->register($request->validated());

        return api_created([
            'access_token' => $result->token,
            'token_type' => TokenConstants::TOKEN_TYPE,
            'expires_in' => TokenConstants::DEFAULT_ACCESS_TOKEN_EXPIRY_MINUTES * 60,
            'user' => new AuthResource($result->user),
        ], 'Inscription réussie');
    }

    /**
     * Déconnexion.
     */
    public function logout(Request $request): JsonResponse {
        $this->authService->logout($request->user());

        return api_success(null, 'Déconnexion réussie');
    }

    /**
     * Utilisateur courant.
     */
    public function me(Request $request): JsonResponse {
        return api_success(
            new AuthResource($this->authService->me($request->user())),
            'Utilisateur récupéré',
        );
    }
}
