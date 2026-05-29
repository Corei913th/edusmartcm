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
 * @author Derrick <ngaha.derrick@nexatec.cm>
 */
class AbsenceControllerTest extends TestCase {
    use RefreshDatabase;

    public function testAuthenticatedUserCanListAbsencesForAnInscription(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        Absence::factory()->count(3)->create(['inscription_id' => $inscription->id]);

        $response = $this->getJson("/api/inscriptions/{$inscription->id}/absences");

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testUnauthenticatedRequestIsRejectedWith401(): void {
        $inscription = Inscription::factory()->create();

        $this->getJson("/api/inscriptions/{$inscription->id}/absences")
            ->assertUnauthorized();
    }

    public function testParentCanViewAbsencesForLinkedStudent(): void {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();
        Absence::factory()->count(2)->create(['inscription_id' => $inscription->id]);

        $this->getJson("/api/inscriptions/{$inscription->id}/absences")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testEnseignantCanCreateAnAbsence(): void {
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

    public function testParentCannotCreateAnAbsence(): void {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();

        $this->postJson('/api/absences', [
            'inscription_id' => $inscription->id,
            'date_absence'   => '2026-05-15',
            'statut'         => StatutAbsence::INJUSTIFIEE->value,
        ])->assertForbidden();
    }

    public function testEleveCannotCreateAnAbsence(): void {
        $eleve = Utilisateur::factory()->create(['role_code' => Role::ELEVE]);
        Sanctum::actingAs($eleve);

        $inscription = Inscription::factory()->create();

        $this->postJson('/api/absences', [
            'inscription_id' => $inscription->id,
            'date_absence'   => '2026-05-16',
            'statut'         => StatutAbsence::EN_ATTENTE->value,
        ])->assertForbidden();
    }

    public function testOfflineAbsenceIsStoredWithSaisieHorsLigneFlag(): void {
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

    public function testSyncEndpointMarksOfflineAbsencesAsSynced(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $absence = Absence::factory()->create([
            'saisie_hors_ligne' => true,
            'sync_at'           => null,
            'created_by'        => $enseignant->id,
        ]);

        $this->patchJson("/api/absences/{$absence->id}/sync")
            ->assertOk();

        $this->assertDatabaseMissing('absences', [
            'id'      => $absence->id,
            'sync_at' => null,
        ]);
    }

    public function testPendingSyncAbsencesAreReturnedViaDedicatedEndpoint(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        Absence::factory()->count(3)->create([
            'saisie_hors_ligne' => true,
            'sync_at'           => null,
            'created_by'        => $enseignant->id,
        ]);

        Absence::factory()->count(2)->create([
            'saisie_hors_ligne' => true,
            'sync_at'           => now(),
            'created_by'        => $enseignant->id,
        ]);

        $this->getJson('/api/absences/pending-sync')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testDirectionCanJustifyAnAbsence(): void {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $absence = Absence::factory()->create(['statut' => StatutAbsence::EN_ATTENTE]);

        $this->patchJson("/api/absences/{$absence->id}", [
            'statut' => StatutAbsence::JUSTIFIEE->value,
        ])->assertOk()
            ->assertJsonPath('data.statut', StatutAbsence::JUSTIFIEE->value);
    }

    public function testAbsenceValidationRejectsMissingDate(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();

        $this->postJson('/api/absences', [
            'inscription_id' => $inscription->id,
            'statut'         => StatutAbsence::INJUSTIFIEE->value,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['date_absence']);
    }

    public function testDeletingAbsenceReturnsNoContent(): void {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $absence = Absence::factory()->create();

        $this->deleteJson("/api/absences/{$absence->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('absences', ['id' => $absence->id]);
    }
}
