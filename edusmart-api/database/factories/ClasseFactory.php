<?php

namespace Database\Factories;

use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Etablissement;
use App\Models\Niveau;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClasseFactory extends Factory {
    protected $model = Classe::class;

    public function definition(): array {
        return [
            'etablissement_id' => Etablissement::factory(),
            'annee_id' => AnneeScolaire::factory(),
            'niveau_id' => Niveau::factory(),
            'nom' => $this->faker->randomElement(['3ème A', '3ème B', '2nde C', '1ère D', 'Tle A']),
            'effectif_max' => $this->faker->numberBetween(30, 60),
        ];
    }
}
