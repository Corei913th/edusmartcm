<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatutAbsence;
use App\Models\Absence;
use App\Models\Inscription;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Integration tests for the Absence controller.
 *
 * Covers: listing, filtering by offline status, RBAC (only authorised roles
 * can mark absences), and the critical offline-first sync scenario.
 *
 * Coverage target: ≥ 80 % on app/Http/Controllers/AbsenceController.php
 *
 * @author Derrick <ngaha.derrick@nexatec.cm>
 */
class AbsenceControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Listing absences
    // -----------------------------------------------------------------------

    public function test_authenticated_user_can_list_absences_for_an_inscription(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        Absence::factory()->count(3)->create(['inscription_id' => $inscription->id]);

        $response = $this->getJson("/api/inscriptions/{$inscription->id}/absences");

        $response->assertOk()
                 ->assertJsonCount(3, 'data');
    }

    public function test_unauthenticated_request_is_rejected_with_401(): void
    {
        $inscription = Inscription::factory()->create();

        $this->getJson("/api/inscriptions/{$inscription->id}/absences")
             ->assertUnauthorized();
    }

    public function test_parent_can_view_absences_for_linked_student(): void
    {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();
        Absence::factory()->count(2)->create(['inscription_id' => $inscription->id]);

        $this->getJson("/api/inscriptions/{$inscription->id}/absences")
             ->assertOk()
             ->assertJsonCount(2, 'data');
    }

    // -----------------------------------------------------------------------
    // Creating absences (RBAC)
    // -----------------------------------------------------------------------

    public function test_enseignant_can_create_an_absence(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();

        $payload = [
            'inscription_id' => $inscription->id,
            'date_absence'   => '2026-05-15',
            'statut'         => StatutAbsence::INJUSTIFIEE->value,
        ];

        $this->postJson('/api/absences', $payload)
             ->assertCreated()
             ->assertJsonPath('data.statut', StatutAbsence::INJUSTIFIEE->value);

        $this->assertDatabaseHas('absences', [
            'inscription_id' => $inscription->id,
            'date_absence'   => '2026-05-15',
        ]);
    }

    public function test_parent_cannot_create_an_absence(): void
    {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();

        $this->postJson('/api/absences', [
            'inscription_id' => $inscription->id,
            'date_absence'   => '2026-05-15',
            'statut'         => StatutAbsence::INJUSTIFIEE->value,
        ])->assertForbidden();
    }

    public function test_eleve_cannot_create_an_absence(): void
    {
        $eleve = Utilisateur::factory()->create(['role_code' => Role::ELEVE]);
        Sanctum::actingAs($eleve);

        $inscription = Inscription::factory()->create();

        $this->postJson('/api/absences', [
            'inscription_id' => $inscription->id,
            'date_absence'   => '2026-05-16',
            'statut'         => StatutAbsence::EN_ATTENTE->value,
        ])->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Offline-first sync scenario (R-02 / WP-2.1)
    // -----------------------------------------------------------------------

    public function test_offline_absence_is_stored_with_saisie_hors_ligne_flag(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();

        $this->postJson('/api/absences', [
            'inscription_id'    => $inscription->id,
            'date_absence'      => '2026-05-20',
            'statut'            => StatutAbsence::EN_ATTENTE->value,
            'saisie_hors_ligne' => true,
        ])->assertCreated()
          ->assertJsonPath('data.saisie_hors_ligne', true);

        $this->assertDatabaseHas('absences', [
            'inscription_id'    => $inscription->id,
            'saisie_hors_ligne' => true,
            'sync_at'           => null,
        ]);
    }

    public function test_sync_endpoint_marks_offline_absences_as_synced(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $absence = Absence::factory()->create([
            'saisie_hors_ligne' => true,
            'sync_at'          => null,
            'created_by'       => $enseignant->id,
        ]);

        $this->patchJson("/api/absences/{$absence->id}/sync")
             ->assertOk();

        $this->assertDatabaseMissing('absences', [
            'id'      => $absence->id,
            'sync_at' => null,
        ]);
    }

    public function test_pending_sync_absences_are_returned_via_dedicated_endpoint(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        Absence::factory()->count(3)->create([
            'saisie_hors_ligne' => true,
            'sync_at'          => null,
            'created_by'       => $enseignant->id,
        ]);

        Absence::factory()->count(2)->create([
            'saisie_hors_ligne' => true,
            'sync_at'          => now(),
            'created_by'       => $enseignant->id,
        ]);

        $this->getJson('/api/absences/pending-sync')
             ->assertOk()
             ->assertJsonCount(3, 'data');
    }

    // -----------------------------------------------------------------------
    // Updating absence status
    // -----------------------------------------------------------------------

    public function test_direction_can_justify_an_absence(): void
    {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $absence = Absence::factory()->create(['statut' => StatutAbsence::EN_ATTENTE]);

        $this->patchJson("/api/absences/{$absence->id}", [
            'statut' => StatutAbsence::JUSTIFIEE->value,
        ])->assertOk()
          ->assertJsonPath('data.statut', StatutAbsence::JUSTIFIEE->value);
    }

    public function test_absence_validation_rejects_missing_date(): void
    {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();

        $this->postJson('/api/absences', [
            'inscription_id' => $inscription->id,
            'statut'         => StatutAbsence::INJUSTIFIEE->value,
            // date_absence intentionally omitted
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['date_absence']);
    }

    public function test_deleting_absence_returns_no_content(): void
    {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $absence = Absence::factory()->create();

        $this->deleteJson("/api/absences/{$absence->id}")
             ->assertNoContent();

        $this->assertDatabaseMissing('absences', ['id' => $absence->id]);
    }
}
