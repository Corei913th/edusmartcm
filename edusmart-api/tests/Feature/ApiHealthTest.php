<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\StatutAbsence;
use App\Enums\TypeEvaluation;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

/**
 * Tests de santé de l'API pour CI/CD
 */
class ApiHealthTest extends TestCase {
    /**
     * Test que l'application Laravel fonctionne
     */
    public function testApplicationReturnsSuccessfulResponse(): void {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test que les routes API sont accessibles
     */
    public function testApiRoutesAreAccessible(): void {
        // Test d'une route qui ne nécessite pas de base de données
        $response = $this->getJson('/api/v1/notes');

        // Doit retourner 401 (non authentifié) et non 404 (route non trouvée) ou 500 (erreur serveur)
        $response->assertStatus(401);
    }

    /**
     * Test que les helpers API fonctionnent
     */
    public function testApiHelpersWork(): void {
        $successResponse = api_success(['test' => 'data'], 'Test message');
        $errorResponse = api_error('Test error');

        $this->assertInstanceOf(JsonResponse::class, $successResponse);
        $this->assertInstanceOf(JsonResponse::class, $errorResponse);

        $this->assertSame(200, $successResponse->getStatusCode());
        $this->assertSame(400, $errorResponse->getStatusCode());
    }

    /**
     * Test que les enums sont accessibles
     */
    public function testEnumsAreAccessible(): void {
        $typeEvaluation = TypeEvaluation::DEVOIR;
        $statutAbsence = StatutAbsence::EN_ATTENTE;

        $this->assertSame('DEVOIR', $typeEvaluation->value);
        $this->assertSame('EN_ATTENTE', $statutAbsence->value);
    }
}
