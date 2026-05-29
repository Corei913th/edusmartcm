<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Bulletin;
use App\Models\Inscription;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Integration tests for the Bulletin controller.
 *
 * Validates that parents can consult published bulletins (offline-first
 * use-case) and that unpublished bulletins are hidden from non-admin roles.
 *
 * @author Derrick <ngaha.derrick@nexatec.cm>
 */
class BulletinControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_access_published_bulletins_for_student(): void
    {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();
        Bulletin::factory()->count(2)->create([
            'inscription_id' => $inscription->id,
            'est_publie'     => true,
            'publie_at'      => now(),
        ]);

        $this->getJson("/api/inscriptions/{$inscription->id}/bulletins")
             ->assertOk()
             ->assertJsonCount(2, 'data');
    }

    public function test_parent_cannot_see_unpublished_bulletins(): void
    {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();
        Bulletin::factory()->count(3)->create([
            'inscription_id' => $inscription->id,
            'est_publie'     => false,
        ]);

        $this->getJson("/api/inscriptions/{$inscription->id}/bulletins")
             ->assertOk()
             ->assertJsonCount(0, 'data');
    }

    public function test_direction_can_see_all_bulletins_including_unpublished(): void
    {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $inscription = Inscription::factory()->create();
        Bulletin::factory()->count(1)->create(['inscription_id' => $inscription->id, 'est_publie' => true]);
        Bulletin::factory()->count(2)->create(['inscription_id' => $inscription->id, 'est_publie' => false]);

        $this->getJson("/api/inscriptions/{$inscription->id}/bulletins")
             ->assertOk()
             ->assertJsonCount(3, 'data');
    }

    public function test_unauthenticated_user_cannot_access_bulletins(): void
    {
        $inscription = Inscription::factory()->create();

        $this->getJson("/api/inscriptions/{$inscription->id}/bulletins")
             ->assertUnauthorized();
    }

    public function test_direction_can_publish_a_bulletin(): void
    {
        $direction = Utilisateur::factory()->create(['role_code' => Role::DIRECTION]);
        Sanctum::actingAs($direction);

        $inscription = Inscription::factory()->create();
        $bulletin    = Bulletin::factory()->create([
            'inscription_id' => $inscription->id,
            'est_publie'     => false,
        ]);

        $this->patchJson("/api/bulletins/{$bulletin->id}/publier")
             ->assertOk()
             ->assertJsonPath('data.est_publie', true);

        $this->assertDatabaseHas('bulletins', [
            'id'         => $bulletin->id,
            'est_publie' => true,
        ]);
    }

    public function test_parent_cannot_publish_a_bulletin(): void
    {
        $parent = Utilisateur::factory()->create(['role_code' => Role::PARENT]);
        Sanctum::actingAs($parent);

        $inscription = Inscription::factory()->create();
        $bulletin    = Bulletin::factory()->create([
            'inscription_id' => $inscription->id,
            'est_publie'     => false,
        ]);

        $this->patchJson("/api/bulletins/{$bulletin->id}/publier")
             ->assertForbidden();
    }
}
