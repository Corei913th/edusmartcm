<?php

namespace Tests\Feature\Auth;

use App\Constants\TokenConstants;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AuthTest extends TestCase {
    use RefreshDatabase;

    private const EMAIL = 'jean.dupont@example.com';

    private const ROUTE_PREFIX = '/api/auth/';

    private const ROUTE_LOGIN = self::ROUTE_PREFIX . 'login';

    private const ROUTE_REGISTER = self::ROUTE_PREFIX . 'register';

    private function createUser(array $overrides = []): Utilisateur {
        return Utilisateur::factory()->create(array_merge([
            'email' => self::EMAIL,
        ], $overrides));
    }

    private function loginResponse(): TestResponse {
        return $this->postJson(self::ROUTE_LOGIN, [
            'email' => self::EMAIL,
            'password' => 'password',
        ]);
    }

    private function registerResponse(array $data = []): TestResponse {
        return $this->postJson(self::ROUTE_REGISTER, array_merge([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => self::EMAIL,
            'password' => 'password',
        ], $data));
    }

    private function tokenFromUser(Utilisateur $user): string {
        return $user->createToken(
            TokenConstants::DEFAULT_ACCESS_TOKEN_NAME,
            [TokenConstants::ABILITY_ALL],
        )->plainTextToken;
    }

    private function withAuth(Utilisateur $user): self {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenFromUser($user),
        ]);
    }

    private function assertAuthSuccess(
        TestResponse $response,
        string $message,
        int $status,
    ): void {
        $response->assertStatus($status)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('success', true)
                    ->where('message', $message)
                    ->where('data.token_type', TokenConstants::TOKEN_TYPE)
                    ->where('data.expires_in', TokenConstants::DEFAULT_ACCESS_TOKEN_EXPIRY_MINUTES * 60)
                    ->where('data.user.email', self::EMAIL)
                    ->whereType('data.access_token', 'string')
                    ->whereType('data.user.id', 'string')
                    ->whereType('data.user.role_code', 'string')
                    ->whereType('data.user.role_libelle', 'string')
                    ->whereType('data.user.est_actif', 'boolean'),
            );
    }

    public function testUserCanRegister(): void {
        $this->assertAuthSuccess(
            $this->registerResponse(),
            'Inscription réussie',
            201,
        );
    }

    public function testUserCannotRegisterWithExistingEmail(): void {
        $this->createUser();

        $this->registerResponse()
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function testUserCanLoginWithValidCredentials(): void {
        $this->createUser();

        $this->assertAuthSuccess(
            $this->loginResponse(),
            'Connexion réussie',
            200,
        );
    }

    public function testUserCannotLoginWithInvalidPassword(): void {
        $this->createUser();

        $this->postJson(self::ROUTE_LOGIN, [
            'email' => self::EMAIL,
            'password' => 'wrong_password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function testUserCannotLoginWithNonexistentEmail(): void {
        $this->postJson(self::ROUTE_LOGIN, [
            'email' => 'unknown@example.com',
            'password' => 'password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function testInactiveUserCannotLogin(): void {
        $this->createUser([
            'est_actif' => false,
            'email' => 'inactive@example.com',
        ]);

        $this->postJson(self::ROUTE_LOGIN, [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function testAuthenticatedUserCanAccessMe(): void {
        $user = $this->createUser();

        $this->withAuth($user)
            ->getJson('/api/auth/me')
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('success', true)
                    ->where('message', 'Utilisateur récupéré')
                    ->where('data.email', self::EMAIL)
                    ->etc(),
            );
    }

    public function testUnauthenticatedUserCannotAccessMe(): void {
        $this->getJson('/api/auth/me')
            ->assertStatus(401);
    }

    public function testAuthenticatedUserCanLogout(): void {
        $user = $this->createUser();

        $this->withAuth($user)
            ->postJson('/api/auth/logout')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Déconnexion réussie');
    }

    public function testUnauthenticatedUserCannotLogout(): void {
        $this->postJson('/api/auth/logout')
            ->assertStatus(401);
    }

    public function testLoginUpdatesLastConnexion(): void {
        $user = $this->createUser(['derniere_connexion' => null]);

        $this->loginResponse();

        $this->assertNotNull($user->fresh()->derniere_connexion);
    }
}
