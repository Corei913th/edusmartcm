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

    private function makeBulletin(Inscription $inscription, Periode $periode, array $overrides = []): Bulletin {
        return Bulletin::create(array_merge([
            'inscription_id'        => $inscription->id,
            'periode_id'            => $periode->id,
            'rang_classe'           => 1,
            'moyenne_generale'      => 14.00,
            'appreciation_generale' => 'Bien.',
            'est_publie'            => false,
        ], $overrides));
    }

    public function testBulletinCanBeCreatedWithCorrectAttributes(): void {
        $inscription = Inscription::factory()->create();
        $periode = Periode::factory()->create();

        $bulletin = $this->makeBulletin($inscription, $periode, [
            'rang_classe'      => 3,
            'moyenne_generale' => 14.75,
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

        $bulletin = $this->makeBulletin($inscription, $periode);

        $this->assertFalse($bulletin->est_publie);
        $this->assertNull($bulletin->publie_at);
    }

    public function testBulletinCanBePublished(): void {
        $inscription = Inscription::factory()->create();
        $periode = Periode::factory()->create();

        $bulletin = $this->makeBulletin($inscription, $periode);

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

        $bulletin = $this->makeBulletin($inscription, $periode);

        $this->assertInstanceOf(Inscription::class, $bulletin->inscription);
        $this->assertSame($inscription->id, $bulletin->inscription->id);
    }

    public function testBulletinBelongsToPeriode(): void {
        $inscription = Inscription::factory()->create();
        $periode = Periode::factory()->create();

        $bulletin = $this->makeBulletin($inscription, $periode);

        $this->assertInstanceOf(Periode::class, $bulletin->periode);
        $this->assertSame($periode->id, $bulletin->periode->id);
    }

    public function testMultipleBulletinsCanExistPerInscription(): void {
        $inscription = Inscription::factory()->create();

        $this->makeBulletin($inscription, Periode::factory()->create(), ['rang_classe' => 1]);
        $this->makeBulletin($inscription, Periode::factory()->create(), ['rang_classe' => 2]);
        $this->makeBulletin($inscription, Periode::factory()->create(), ['rang_classe' => 3]);

        $this->assertCount(3, Bulletin::where('inscription_id', $inscription->id)->get());
    }
}
