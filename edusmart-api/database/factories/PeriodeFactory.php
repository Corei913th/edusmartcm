<?php

namespace Database\Factories;

use App\Models\AnneeScolaire;
use App\Models\Periode;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodeFactory extends Factory
{
    protected $model = Periode::class;

    public function definition(): array
    {
        return [
            'annee_id' => AnneeScolaire::factory(),
            'numero' => $this->faker->numberBetween(1, 3),
            'libelle' => $this->faker->randomElement(['Premier trimestre', 'Deuxième trimestre', 'Troisième trimestre']),
            'date_debut' => $this->faker->date(),
            'date_fin' => $this->faker->date(),
        ];
    }
}
