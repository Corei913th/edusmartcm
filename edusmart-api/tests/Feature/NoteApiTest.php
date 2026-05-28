<?php

namespace Tests\Feature;

use App\Enums\TypeEvaluation;
use App\Models\Note;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Tests d'intégration pour l'API des notes
 *
 * Teste les endpoints CRUD des notes avec authentification Sanctum.
 */
class NoteApiTest extends TestCase {
    use RefreshDatabase;

    /**
     * Utilisateur authentifié pour les tests
     */
    private Utilisateur $user;

    /**
     * Configuration avant chaque test
     */
    protected function setUp(): void {
        parent::setUp();

        // Créer un utilisateur de test et l'authentifier
        $this->user = Utilisateur::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /**
     * Test de récupération de la liste des notes
     */
    public function testCanListNotes(): void {
        // Créer quelques notes de test
        Note::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/notes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'inscription_id',
                            'affectation_id',
                            'periode_id',
                            'type_evaluation',
                            'note',
                            'coefficient',
                            'date_evaluation',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total',
                    ],
                ],
            ]);
    }

    /**
     * Test de création d'une note
     */
    public function testCanCreateNote(): void {
        $noteData = [
            'inscription_id' => '550e8400-e29b-41d4-a716-446655440000',
            'affectation_id' => '550e8400-e29b-41d4-a716-446655440001',
            'periode_id' => '550e8400-e29b-41d4-a716-446655440002',
            'type_evaluation' => TypeEvaluation::DEVOIR->value,
            'note' => 15.5,
            'coefficient' => 2,
            'date_evaluation' => '2024-01-15',
        ];

        $response = $this->postJson('/api/v1/notes', $noteData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'type_evaluation',
                    'note',
                    'coefficient',
                ],
            ]);
    }

    /**
     * Test de validation lors de la création d'une note
     */
    public function testNoteCreationValidation(): void {
        $invalidData = [
            'note' => 25, // Note invalide (> 20)
            'coefficient' => 0, // Coefficient invalide (< 1)
        ];

        $response = $this->postJson('/api/v1/notes', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['inscription_id', 'affectation_id', 'periode_id', 'type_evaluation', 'note', 'coefficient', 'date_evaluation']);
    }

    /**
     * Test de récupération d'une note spécifique
     */
    public function testCanShowNote(): void {
        $note = Note::factory()->create();

        $response = $this->getJson("/api/v1/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'type_evaluation',
                    'note',
                    'coefficient',
                ],
            ]);
    }

    /**
     * Test de mise à jour d'une note
     */
    public function testCanUpdateNote(): void {
        $note = Note::factory()->create();

        $updateData = [
            'note' => 18.0,
            'coefficient' => 3,
        ];

        $response = $this->putJson("/api/v1/notes/{$note->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    /**
     * Test de suppression d'une note
     */
    public function testCanDeleteNote(): void {
        $note = Note::factory()->create();

        $response = $this->deleteJson("/api/v1/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test d'accès non autorisé sans authentification
     */
    public function testRequiresAuthentication(): void {
        // Déconnecter l'utilisateur
        Sanctum::actingAs(null);

        $response = $this->getJson('/api/v1/notes');

        $response->assertStatus(401);
    }
}
