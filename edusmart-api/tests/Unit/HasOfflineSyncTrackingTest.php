<?php

namespace Tests\Unit;

use App\Enums\StatutAbsence;
use App\Enums\TypeEvaluation;
use App\Models\Absence;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for HasOfflineSyncTracking trait.
 *
 * Validates the three query scopes used for offline-first synchronisation:
 * - pendingSync  : saisie_hors_ligne = true AND sync_at IS NULL
 * - synced       : sync_at IS NOT NULL
 * - offlineOnly  : saisie_hors_ligne = true
 *
 * @author Derrick <ngaha.derrick@nexatec.cm>
 */
class HasOfflineSyncTrackingTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Absence model — scopePendingSync
    // -----------------------------------------------------------------------

    public function test_pending_sync_scope_returns_only_unsynced_offline_absences(): void
    {
        // Offline, not yet synced → must appear
        $pending = Absence::factory()->create([
            'statut'           => StatutAbsence::EN_ATTENTE,
            'saisie_hors_ligne' => true,
            'sync_at'          => null,
        ]);

        // Offline, already synced → must NOT appear
        Absence::factory()->create([
            'statut'           => StatutAbsence::JUSTIFIEE,
            'saisie_hors_ligne' => true,
            'sync_at'          => now(),
        ]);

        // Online entry → must NOT appear
        Absence::factory()->create([
            'statut'           => StatutAbsence::INJUSTIFIEE,
            'saisie_hors_ligne' => false,
            'sync_at'          => null,
        ]);

        $results = Absence::pendingSync()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($pending));
    }

    public function test_pending_sync_scope_returns_empty_when_all_absences_are_synced(): void
    {
        Absence::factory()->count(3)->create([
            'saisie_hors_ligne' => true,
            'sync_at'          => now(),
        ]);

        $this->assertCount(0, Absence::pendingSync()->get());
    }

    // -----------------------------------------------------------------------
    // Absence model — scopeSynced
    // -----------------------------------------------------------------------

    public function test_synced_scope_returns_only_absences_with_sync_at_set(): void
    {
        $synced = Absence::factory()->create([
            'saisie_hors_ligne' => true,
            'sync_at'          => now(),
        ]);

        Absence::factory()->create([
            'saisie_hors_ligne' => true,
            'sync_at'          => null,
        ]);

        $results = Absence::synced()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($synced));
    }

    // -----------------------------------------------------------------------
    // Absence model — scopeOfflineOnly
    // -----------------------------------------------------------------------

    public function test_offline_only_scope_returns_all_offline_absences_regardless_of_sync_status(): void
    {
        // Two offline records (one synced, one pending)
        Absence::factory()->create(['saisie_hors_ligne' => true, 'sync_at' => now()]);
        Absence::factory()->create(['saisie_hors_ligne' => true, 'sync_at' => null]);

        // One online record → must NOT appear
        Absence::factory()->create(['saisie_hors_ligne' => false, 'sync_at' => null]);

        $this->assertCount(2, Absence::offlineOnly()->get());
    }

    // -----------------------------------------------------------------------
    // Note model — same scopes
    // -----------------------------------------------------------------------

    public function test_note_pending_sync_scope_isolates_unsynced_offline_notes(): void
    {
        $pending = Note::factory()->create([
            'type_evaluation'  => TypeEvaluation::DEVOIR,
            'saisie_hors_ligne' => true,
            'sync_at'          => null,
        ]);

        Note::factory()->create([
            'type_evaluation'  => TypeEvaluation::COMPOSITION,
            'saisie_hors_ligne' => true,
            'sync_at'          => now(),
        ]);

        Note::factory()->create([
            'type_evaluation'  => TypeEvaluation::ORAL,
            'saisie_hors_ligne' => false,
            'sync_at'          => null,
        ]);

        $results = Note::pendingSync()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($pending));
    }

    public function test_note_synced_scope_counts_correctly(): void
    {
        Note::factory()->count(4)->create(['saisie_hors_ligne' => true, 'sync_at' => now()]);
        Note::factory()->count(2)->create(['saisie_hors_ligne' => true, 'sync_at' => null]);

        $this->assertCount(4, Note::synced()->get());
    }

    public function test_note_offline_only_scope_includes_synced_and_pending(): void
    {
        Note::factory()->count(3)->create(['saisie_hors_ligne' => true, 'sync_at' => now()]);
        Note::factory()->count(2)->create(['saisie_hors_ligne' => true, 'sync_at' => null]);
        Note::factory()->count(5)->create(['saisie_hors_ligne' => false, 'sync_at' => null]);

        $this->assertCount(5, Note::offlineOnly()->get());
    }
}
