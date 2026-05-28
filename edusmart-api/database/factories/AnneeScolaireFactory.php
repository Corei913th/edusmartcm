<?php

namespace Database\Factories;

use App\Models\AnneeScolaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnneeScolaireFactory extends Factory
{
    protected $model = AnneeScolaire::class;

    public function definition(): array
    {
        $year = $this->faker->numberBetween(2025, 2030);

        return [
            'libelle' => $year . '-' . ($year + 1),
            'date_debut' => $year . '-09-01',
            'date_fin' => ($year + 1) . '-08-31',
            'est_active' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['est_active' => true]);
    }
}
