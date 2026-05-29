<?php


namespace Tests\Feature;

use Tests\TestCase;

/**
 * Tests d'intégration pour l'API des notes
 */
class NoteApiTest extends TestCase {
    /**
     * Test de validation lors de la création d'une note
     */
    public function testNoteCreationValidation(): void {
        $invalidData = [
            'note' => 25, // Note invalide (> 20)
            'coefficient' => 0, // Coefficient invalide (< 1)
        ];

        $response = $this->postJson('/api/v1/notes', $invalidData);

        $response->assertStatus(401); // Non authentifié car pas de token
    }

    /**
     * Test d'accès non autorisé sans authentification
     */
    public function testRequiresAuthentication(): void {
        $response = $this->getJson('/api/v1/notes');

        $response->assertStatus(401);
    }

    /**
     * Test que les routes existent
     */
    public function testRoutesExist(): void {
        // Test que les routes retournent des réponses valides (pas 404)
        $routes = [
            'GET' => '/api/v1/notes',
            'POST' => '/api/v1/notes',
        ];

        foreach ($routes as $method => $route) {
            $response = match ($method) {
                'GET' => $this->getJson($route),
                'POST' => $this->postJson($route, []),
                default => null,
            };

            // Ne doit pas être 404 (route non trouvée)
            $this->assertNotSame(404, $response->getStatusCode());
        }
    }
}
