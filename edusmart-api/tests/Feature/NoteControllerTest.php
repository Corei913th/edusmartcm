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
 * Integration tests for the Note controller.
 *
 * Covers: CRUD operations, RBAC, note range validation (0–20),
 * offline-first creation and sync, and parent read access.
 *
 * Coverage target: ≥ 80 % on app/Http/Controllers/NoteController.php
 *
 * @author Derrick <ngaha.derrick@nexatec.cm>
 */
class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Listing notes
    // -----------------------------------------------------------------------

    public function test_enseignant_can_list_notes_for_inscription(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        Note::factory()->count(4)->create(['inscription_id' => $inscription->id]);

        $this->getJson("/api/inscriptions/{$inscription->id}/notes")
             ->assertOk()
             ->assertJsonCount(4, 'data');
    }

    public function test_unauthenticated_request_cannot_list_notes(): void
    {
        $inscription = Inscription::factory()->create();

        $this->getJson("/api/inscriptions/{$inscription->id}/notes")
             ->assertUnauthorized();
    }

    public function test_parent_can_read_notes_of_linked_student(): void
    {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();
        Note::factory()->count(2)->create(['inscription_id' => $inscription->id]);

        $this->getJson("/api/inscriptions/{$inscription->id}/notes")
             ->assertOk()
             ->assertJsonCount(2, 'data');
    }

    // -----------------------------------------------------------------------
    // Creating notes (RBAC + validation)
    // -----------------------------------------------------------------------

    public function test_enseignant_can_create_a_note(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription  = Inscription::factory()->create();
        $affectation  = AffectationEnseignement::factory()->create();
        $periode      = Periode::factory()->create();

        $payload = [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::DEVOIR->value,
            'note'            => 14.5,
            'coefficient'     => 2,
            'date_evaluation' => '2026-05-13',
        ];

        $this->postJson('/api/notes', $payload)
             ->assertCreated()
             ->assertJsonPath('data.note', '14.50');

        $this->assertDatabaseHas('notes', [
            'inscription_id'  => $inscription->id,
            'date_evaluation' => '2026-05-13',
        ]);
    }

    public function test_parent_cannot_create_a_note(): void
    {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode     = Periode::factory()->create();

        $this->postJson('/api/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::DEVOIR->value,
            'note'            => 12.0,
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-13',
        ])->assertForbidden();
    }

    public function test_eleve_cannot_create_a_note(): void
    {
        $eleve = Utilisateur::factory()->create(['role_code' => Role::ELEVE]);
        Sanctum::actingAs($eleve);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode     = Periode::factory()->create();

        $this->postJson('/api/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::ORAL->value,
            'note'            => 18.0,
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-14',
        ])->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Validation: note range 0–20 (SQL CHECK constraint mirror)
    // -----------------------------------------------------------------------

    public function test_note_above_20_is_rejected_with_422(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode     = Periode::factory()->create();

        $this->postJson('/api/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::COMPOSITION->value,
            'note'            => 21.0,   // invalid
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-15',
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['note']);
    }

    public function test_note_below_0_is_rejected_with_422(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode     = Periode::factory()->create();

        $this->postJson('/api/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::EXAMEN->value,
            'note'            => -1.0,   // invalid
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-16',
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['note']);
    }

    public function test_note_boundary_value_20_is_accepted(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode     = Periode::factory()->create();

        $this->postJson('/api/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::TP->value,
            'note'            => 20.0,
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-17',
        ])->assertCreated();
    }

    public function test_note_boundary_value_0_is_accepted(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode     = Periode::factory()->create();

        $this->postJson('/api/notes', [
            'inscription_id'  => $inscription->id,
            'affectation_id'  => $affectation->id,
            'periode_id'      => $periode->id,
            'type_evaluation' => TypeEvaluation::DEVOIR->value,
            'note'            => 0.0,
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-18',
        ])->assertCreated();
    }

    // -----------------------------------------------------------------------
    // Offline-first scenario (R-02 / WP-2.1)
    // -----------------------------------------------------------------------

    public function test_note_can_be_created_offline_with_sync_flag(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode     = Periode::factory()->create();

        $this->postJson('/api/notes', [
            'inscription_id'    => $inscription->id,
            'affectation_id'    => $affectation->id,
            'periode_id'        => $periode->id,
            'type_evaluation'   => TypeEvaluation::DEVOIR->value,
            'note'              => 16.0,
            'coefficient'       => 2,
            'date_evaluation'   => '2026-05-21',
            'saisie_hors_ligne' => true,
        ])->assertCreated()
          ->assertJsonPath('data.saisie_hors_ligne', true);

        $this->assertDatabaseHas('notes', [
            'inscription_id'    => $inscription->id,
            'saisie_hors_ligne' => true,
            'sync_at'           => null,
        ]);
    }

    public function test_sync_endpoint_updates_sync_at_for_offline_note(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $note = Note::factory()->create([
            'saisie_hors_ligne' => true,
            'sync_at'          => null,
            'created_by'       => $enseignant->id,
        ]);

        $this->patchJson("/api/notes/{$note->id}/sync")
             ->assertOk();

        $this->assertDatabaseMissing('notes', [
            'id'      => $note->id,
            'sync_at' => null,
        ]);
    }

    public function test_pending_sync_notes_endpoint_returns_only_unsynced_offline_notes(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        Note::factory()->count(5)->create([
            'saisie_hors_ligne' => true,
            'sync_at'          => null,
            'created_by'       => $enseignant->id,
        ]);

        Note::factory()->count(3)->create([
            'saisie_hors_ligne' => false,
            'sync_at'          => null,
            'created_by'       => $enseignant->id,
        ]);

        $this->getJson('/api/notes/pending-sync')
             ->assertOk()
             ->assertJsonCount(5, 'data');
    }

    // -----------------------------------------------------------------------
    // Updating & deleting
    // -----------------------------------------------------------------------

    public function test_enseignant_can_update_their_own_note(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $note = Note::factory()->create([
            'note'       => 10.0,
            'created_by' => $enseignant->id,
        ]);

        $this->patchJson("/api/notes/{$note->id}", ['note' => 13.5])
             ->assertOk()
             ->assertJsonPath('data.note', '13.50');
    }

    public function test_direction_can_delete_a_note(): void
    {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $note = Note::factory()->create();

        $this->deleteJson("/api/notes/{$note->id}")
             ->assertNoContent();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }
}
