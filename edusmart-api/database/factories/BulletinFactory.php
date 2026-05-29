<?php

namespace Database\Factories;

use App\Models\Bulletin;
use App\Models\Inscription;
use App\Models\Periode;
use Illuminate\Database\Eloquent\Factories\Factory;

class BulletinFactory extends Factory {
    protected $model = Bulletin::class;

    public function definition(): array {
        return [
            'inscription_id'        => Inscription::factory(),
            'periode_id'            => Periode::factory(),
            'rang_classe'           => $this->faker->numberBetween(1, 50),
            'moyenne_generale'      => $this->faker->randomFloat(2, 5, 18),
            'appreciation_generale' => $this->faker->sentence(),
            'est_publie'            => false,
            'publie_at'             => null,
        ];
    }

    public function publie(): static {
        return $this->state(fn (array $attributes) => [
            'est_publie' => true,
            'publie_at'  => now(),
        ]);
    }
}
