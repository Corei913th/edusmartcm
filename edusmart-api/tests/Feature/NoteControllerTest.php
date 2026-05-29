<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\TypeEvaluation;
use App\Models\AffectationEnseignement;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\Periode;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Integration tests for the Note controller (REST /api/v1/notes).
 *
 * Covers: listing, CRUD, note range validation (0-20),
 * offline-first creation flag, and parent read access.
 *
 * @author Derrick <ngaha.derrick@nexatec.cm>
 */
class NoteControllerTest extends TestCase {
    use RefreshDatabase;

    public function testEnseignantCanListNotes(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        Note::factory()->count(4)->create(['created_by' => $enseignant->id]);

        $response = $this->getJson('/api/v1/notes');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(4, count($response->json('data')));
    }

    public function testUnauthenticatedRequestIsRejectedWith401(): void {
        $this->getJson('/api/v1/notes')->assertUnauthorized();
    }

    public function testParentCanListNotes(): void {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $this->getJson('/api/v1/notes')->assertOk();
    }

    public function testEnseignantCanCreateANote(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode = Periode::factory()->create();

        $payload = [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::DEVOIR->value,
            'note'            => 15.5,
            'coefficient'     => 2,
            'date_evaluation' => '2026-05-15',
        ];

        $this->postJson('/api/v1/notes', $payload)
            ->assertCreated();

        $this->assertDatabaseHas('notes', [
            'inscription_id' => $inscription->id,
            'note'           => 15.5,
        ]);
    }

    public function testNoteValidationRejectsNoteAbove20(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode = Periode::factory()->create();

        $this->postJson('/api/v1/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::DEVOIR->value,
            'note'            => 21,
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-15',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['note']);
    }

    public function testNoteValidationRejectsNegativeNote(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode = Periode::factory()->create();

        $this->postJson('/api/v1/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::DEVOIR->value,
            'note'            => -1,
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-15',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['note']);
    }

    public function testNoteValidationRejectsMissingRequiredFields(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $this->postJson('/api/v1/notes', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['inscription_id', 'affectation_id', 'periode_id', 'note']);
    }

    public function testOfflineNoteIsStoredWithSaisieHorsLigneFlag(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode = Periode::factory()->create();

        $this->postJson('/api/v1/notes', [
            'inscription_id'    => $inscription->id,
            'affectation_id'    => $affectation->id,
            'periode_id'        => $periode->id,
            'type_evaluation'   => TypeEvaluation::COMPOSITION->value,
            'note'              => 12.0,
            'coefficient'       => 3,
            'date_evaluation'   => '2026-05-20',
            'saisie_hors_ligne' => true,
        ])->assertCreated();

        $this->assertDatabaseHas('notes', [
            'inscription_id'    => $inscription->id,
            'saisie_hors_ligne' => true,
        ]);
    }

    public function testShowReturnsNoteDetails(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $note = Note::factory()->create(['created_by' => $enseignant->id]);

        $this->getJson("/api/v1/notes/{$note->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $note->id);
    }

    public function testEnseignantCanUpdateANote(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $note = Note::factory()->create(['created_by' => $enseignant->id]);

        $this->putJson("/api/v1/notes/{$note->id}", [
            'note' => 18.0,
        ])->assertOk();
    }

    public function testDeletingNoteReturnsSuccess(): void {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $note = Note::factory()->create(['created_by' => $direction->id]);

        $this->deleteJson("/api/v1/notes/{$note->id}")
            ->assertOk();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function testFilteringNotesByInscriptionId(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        Note::factory()->count(3)->create([
            'inscription_id' => $inscription->id,
            'created_by'     => $enseignant->id,
        ]);

        Note::factory()->count(2)->create(['created_by' => $enseignant->id]);

        $response = $this->getJson("/api/v1/notes?inscription_id={$inscription->id}");

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    public function testNoteAcceptsZeroValue(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode = Periode::factory()->create();

        $this->postJson('/api/v1/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::ORAL->value,
            'note'            => 0,
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-15',
        ])->assertCreated();
    }

    public function testNoteAcceptsMaxValue(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode = Periode::factory()->create();

        $this->postJson('/api/v1/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::EXAMEN->value,
            'note'            => 20,
            'coefficient'     => 4,
            'date_evaluation' => '2026-05-15',
        ])->assertCreated();
    }
}
