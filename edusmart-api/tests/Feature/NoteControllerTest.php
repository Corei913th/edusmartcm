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
 * Covers: CRUD, RBAC, note range validation (0-20),
 * offline-first creation and sync, parent read access.
 *
 * @author Derrick <ngaha.derrick@nexatec.cm>
 */
class NoteControllerTest extends TestCase {
    use RefreshDatabase;

    public function testEnseignantCanListNotesForInscription(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        Note::factory()->count(4)->create(['inscription_id' => $inscription->id]);

        $this->getJson("/api/inscriptions/{$inscription->id}/notes")
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function testUnauthenticatedRequestCannotListNotes(): void {
        $inscription = Inscription::factory()->create();

        $this->getJson("/api/inscriptions/{$inscription->id}/notes")
            ->assertUnauthorized();
    }

    public function testParentCanReadNotesOfLinkedStudent(): void {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();
        Note::factory()->count(2)->create(['inscription_id' => $inscription->id]);

        $this->getJson("/api/inscriptions/{$inscription->id}/notes")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testEnseignantCanCreateANote(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();
        $periode     = Periode::factory()->create();

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

    public function testParentCannotCreateANote(): void {
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

    public function testEleveCannotCreateANote(): void {
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

    public function testNoteAbove20IsRejectedWith422(): void {
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
            'note'            => 21.0,
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-15',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['note']);
    }

    public function testNoteBelow0IsRejectedWith422(): void {
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
            'note'            => -1.0,
            'coefficient'     => 1,
            'date_evaluation' => '2026-05-16',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['note']);
    }

    public function testNoteBoundaryValue20IsAccepted(): void {
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

    public function testNoteBoundaryValue0IsAccepted(): void {
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

    public function testNoteCanBeCreatedOfflineWithSyncFlag(): void {
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

    public function testSyncEndpointUpdatesSyncAtForOfflineNote(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $note = Note::factory()->create([
            'saisie_hors_ligne' => true,
            'sync_at'           => null,
            'created_by'        => $enseignant->id,
        ]);

        $this->patchJson("/api/notes/{$note->id}/sync")
            ->assertOk();

        $this->assertDatabaseMissing('notes', [
            'id'      => $note->id,
            'sync_at' => null,
        ]);
    }

    public function testPendingSyncNotesEndpointReturnsOnlyUnsyncedOfflineNotes(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        Note::factory()->count(5)->create([
            'saisie_hors_ligne' => true,
            'sync_at'           => null,
            'created_by'        => $enseignant->id,
        ]);

        Note::factory()->count(3)->create([
            'saisie_hors_ligne' => false,
            'sync_at'           => null,
            'created_by'        => $enseignant->id,
        ]);

        $this->getJson('/api/notes/pending-sync')
            ->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function testEnseignantCanUpdateTheirOwnNote(): void {
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

    public function testDirectionCanDeleteANote(): void {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $note = Note::factory()->create();

        $this->deleteJson("/api/notes/{$note->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }
}
