<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatutAbsence;
use App\Models\Absence;
use App\Models\AffectationEnseignement;
use App\Models\Inscription;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Integration tests for the Absence controller (REST /api/v1/absences).
 *
 * Covers: listing with filters, CRUD operations, validation,
 * and the offline-first saisie_hors_ligne flag.
 *
 * @author Derrick <ngaha.derrick@nexatec.cm>
 */
class AbsenceControllerTest extends TestCase {
    use RefreshDatabase;

    public function testAuthenticatedUserCanListAbsences(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        Absence::factory()->count(3)->create(['created_by' => $enseignant->id]);

        $response = $this->getJson('/api/v1/absences');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(3, count($response->json('data')));
    }

    public function testUnauthenticatedRequestIsRejectedWith401(): void {
        $this->getJson('/api/v1/absences')->assertUnauthorized();
    }

    public function testParentCanListAbsences(): void {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $this->getJson('/api/v1/absences')->assertOk();
    }

    public function testEnseignantCanCreateAnAbsence(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();

        $payload = [
            'inscription_id' => $inscription->id,
            'affectation_id' => $affectation->id,
            'date_absence'   => '2026-05-15',
            'heure_debut'    => '08:00',
            'heure_fin'      => '10:00',
            'duree_heures'   => 2,
            'statut'         => StatutAbsence::INJUSTIFIEE->value,
        ];

        $this->postJson('/api/v1/absences', $payload)
            ->assertCreated();

        $this->assertDatabaseHas('absences', [
            'inscription_id' => $inscription->id,
            'date_absence'   => '2026-05-15',
        ]);
    }

    public function testAbsenceValidationRejectsMissingRequiredFields(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $this->postJson('/api/v1/absences', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['inscription_id', 'affectation_id', 'date_absence']);
    }

    public function testAbsenceValidationRejectsMissingDate(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();

        $this->postJson('/api/v1/absences', [
            'inscription_id' => $inscription->id,
            'affectation_id' => $affectation->id,
            'statut'         => StatutAbsence::INJUSTIFIEE->value,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['date_absence']);
    }

    public function testOfflineAbsenceIsStoredWithSaisieHorsLigneFlag(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        $affectation = AffectationEnseignement::factory()->create();

        $this->postJson('/api/v1/absences', [
            'inscription_id'    => $inscription->id,
            'affectation_id'    => $affectation->id,
            'date_absence'      => '2026-05-20',
            'heure_debut'       => '08:00',
            'heure_fin'         => '09:00',
            'duree_heures'      => 1,
            'statut'            => StatutAbsence::EN_ATTENTE->value,
            'saisie_hors_ligne' => true,
        ])->assertCreated();

        $this->assertDatabaseHas('absences', [
            'inscription_id'    => $inscription->id,
            'saisie_hors_ligne' => true,
        ]);
    }

    public function testEnseignantCanUpdateAnAbsence(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $absence = Absence::factory()->create([
            'statut'     => StatutAbsence::EN_ATTENTE,
            'created_by' => $enseignant->id,
        ]);

        $this->putJson("/api/v1/absences/{$absence->id}", [
            'statut' => StatutAbsence::JUSTIFIEE->value,
        ])->assertOk();
    }

    public function testShowReturnsAbsenceDetails(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $absence = Absence::factory()->create(['created_by' => $enseignant->id]);

        $this->getJson("/api/v1/absences/{$absence->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $absence->id);
    }

    public function testDeletingAbsenceReturnsSuccess(): void {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $absence = Absence::factory()->create(['created_by' => $direction->id]);

        $this->deleteJson("/api/v1/absences/{$absence->id}")
            ->assertOk();

        $this->assertDatabaseMissing('absences', ['id' => $absence->id]);
    }

    public function testFilteringAbsencesByInscriptionId(): void {
        $enseignant = Utilisateur::factory()->create(['role_code' => Role::ENSEIGNANT]);
        Sanctum::actingAs($enseignant);

        $inscription = Inscription::factory()->create();
        Absence::factory()->count(3)->create([
            'inscription_id' => $inscription->id,
            'created_by'     => $enseignant->id,
        ]);

        Absence::factory()->count(2)->create(['created_by' => $enseignant->id]);

        $response = $this->getJson("/api/v1/absences?inscription_id={$inscription->id}");

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }
}
