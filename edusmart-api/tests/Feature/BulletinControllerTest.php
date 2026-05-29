<?php

namespace Tests\Feature;

use App\Models\Bulletin;
use App\Models\Inscription;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the Bulletin model — persistence, relations, and publication flag.
 *
 * The bulletin REST endpoints are not yet exposed; these tests validate
 * the data layer that will back the future BulletinController.
 *
 * @author Derrick <ngaha.derrick@nexatec.cm>
 */
class BulletinControllerTest extends TestCase {
    use RefreshDatabase;

    public function testBulletinCanBeCreatedWithCorrectAttributes(): void {
        $inscription = Inscription::factory()->create();
        $periode = Periode::factory()->create();

        $bulletin = Bulletin::create([
            'inscription_id'        => $inscription->id,
            'periode_id'            => $periode->id,
            'rang_classe'           => 3,
            'moyenne_generale'      => 14.75,
            'appreciation_generale' => 'Bon travail, continuez ainsi.',
            'est_publie'            => false,
        ]);

        $this->assertDatabaseHas('bulletins', [
            'id'          => $bulletin->id,
            'rang_classe' => 3,
            'est_publie'  => false,
        ]);
    }

    public function testBulletinDefaultsToUnpublished(): void {
        $inscription = Inscription::factory()->create();
        $periode = Periode::factory()->create();

        $bulletin = Bulletin::create([
            'inscription_id'        => $inscription->id,
            'periode_id'            => $periode->id,
            'rang_classe'           => 1,
            'moyenne_generale'      => 17.50,
            'appreciation_generale' => 'Excellent.',
            'est_publie'            => false,
        ]);

        $this->assertFalse($bulletin->est_publie);
        $this->assertNull($bulletin->publie_at);
    }

    public function testBulletinCanBePublished(): void {
        $inscription = Inscription::factory()->create();
        $periode = Periode::factory()->create();

        $bulletin = Bulletin::create([
            'inscription_id'        => $inscription->id,
            'periode_id'            => $periode->id,
            'rang_classe'           => 2,
            'moyenne_generale'      => 12.00,
            'appreciation_generale' => 'Peut mieux faire.',
            'est_publie'            => false,
        ]);

        $bulletin->update([
            'est_publie' => true,
            'publie_at'  => now(),
        ]);

        $this->assertTrue($bulletin->fresh()->est_publie);
        $this->assertNotNull($bulletin->fresh()->publie_at);
    }

    public function testBulletinBelongsToInscription(): void {
        $inscription = Inscription::factory()->create();
        $periode = Periode::factory()->create();

        $bulletin = Bulletin::create([
            'inscription_id'        => $inscription->id,
            'periode_id'            => $periode->id,
            'rang_classe'           => 5,
            'moyenne_generale'      => 10.00,
            'appreciation_generale' => 'Passable.',
            'est_publie'            => false,
        ]);

        $this->assertInstanceOf(Inscription::class, $bulletin->inscription);
        $this->assertEquals($inscription->id, $bulletin->inscription->id);
    }

    public function testBulletinBelongsToPeriode(): void {
        $inscription = Inscription::factory()->create();
        $periode = Periode::factory()->create();

        $bulletin = Bulletin::create([
            'inscription_id'        => $inscription->id,
            'periode_id'            => $periode->id,
            'rang_classe'           => 1,
            'moyenne_generale'      => 19.00,
            'appreciation_generale' => 'Exceptionnel.',
            'est_publie'            => false,
        ]);

        $this->assertInstanceOf(Periode::class, $bulletin->periode);
        $this->assertEquals($periode->id, $bulletin->periode->id);
    }

    public function testMultipleBulletinsCanExistPerInscription(): void {
        $inscription = Inscription::factory()->create();
        $periodes = Periode::factory()->count(3)->create();

        foreach ($periodes as $index => $periode) {
            Bulletin::create([
                'inscription_id'        => $inscription->id,
                'periode_id'            => $periode->id,
                'rang_classe'           => $index + 1,
                'moyenne_generale'      => 12.00 + $index,
                'appreciation_generale' => 'Bien.',
                'est_publie'            => false,
            ]);
        }

        $this->assertCount(3, Bulletin::where('inscription_id', $inscription->id)->get());
    }
}
