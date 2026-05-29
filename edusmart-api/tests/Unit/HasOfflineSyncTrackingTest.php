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
class HasOfflineSyncTrackingTest extends TestCase {
    use RefreshDatabase;

    public function testPendingSyncScopeReturnsOnlyUnsyncedOfflineAbsences(): void {
        $pending = Absence::factory()->create([
            'statut'            => StatutAbsence::EN_ATTENTE,
            'saisie_hors_ligne' => true,
            'sync_at'           => null,
        ]);

        Absence::factory()->create([
            'statut'            => StatutAbsence::JUSTIFIEE,
            'saisie_hors_ligne' => true,
            'sync_at'           => now(),
        ]);

        Absence::factory()->create([
            'statut'            => StatutAbsence::INJUSTIFIEE,
            'saisie_hors_ligne' => false,
            'sync_at'           => null,
        ]);

        $results = Absence::pendingSync()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($pending));
    }

    public function testPendingSyncScopeReturnsEmptyWhenAllAbsencesAreSynced(): void {
        Absence::factory()->count(3)->create([
            'saisie_hors_ligne' => true,
            'sync_at'           => now(),
        ]);

        $this->assertCount(0, Absence::pendingSync()->get());
    }

    public function testSyncedScopeReturnsOnlyAbsencesWithSyncAtSet(): void {
        $synced = Absence::factory()->create([
            'saisie_hors_ligne' => true,
            'sync_at'           => now(),
        ]);

        Absence::factory()->create([
            'saisie_hors_ligne' => true,
            'sync_at'           => null,
        ]);

        $results = Absence::synced()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($synced));
    }

    public function testOfflineOnlyScopeReturnsAllOfflineAbsencesRegardlessOfSyncStatus(): void {
        Absence::factory()->create(['saisie_hors_ligne' => true, 'sync_at' => now()]);
        Absence::factory()->create(['saisie_hors_ligne' => true, 'sync_at' => null]);
        Absence::factory()->create(['saisie_hors_ligne' => false, 'sync_at' => null]);

        $this->assertCount(2, Absence::offlineOnly()->get());
    }

    public function testNotePendingSyncScopeIsolatesUnsyncedOfflineNotes(): void {
        $pending = Note::factory()->create([
            'type_evaluation'   => TypeEvaluation::DEVOIR,
            'saisie_hors_ligne' => true,
            'sync_at'           => null,
        ]);

        Note::factory()->create([
            'type_evaluation'   => TypeEvaluation::COMPOSITION,
            'saisie_hors_ligne' => true,
            'sync_at'           => now(),
        ]);

        Note::factory()->create([
            'type_evaluation'   => TypeEvaluation::ORAL,
            'saisie_hors_ligne' => false,
            'sync_at'           => null,
        ]);

        $results = Note::pendingSync()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($pending));
    }

    public function testNoteSyncedScopeCountsCorrectly(): void {
        Note::factory()->count(4)->create(['saisie_hors_ligne' => true, 'sync_at' => now()]);
        Note::factory()->count(2)->create(['saisie_hors_ligne' => true, 'sync_at' => null]);

        $this->assertCount(4, Note::synced()->get());
    }

    public function testNoteOfflineOnlyScopeIncludesSyncedAndPending(): void {
        Note::factory()->count(3)->create(['saisie_hors_ligne' => true, 'sync_at' => now()]);
        Note::factory()->count(2)->create(['saisie_hors_ligne' => true, 'sync_at' => null]);
        Note::factory()->count(5)->create(['saisie_hors_ligne' => false, 'sync_at' => null]);

        $this->assertCount(5, Note::offlineOnly()->get());
    }
}
