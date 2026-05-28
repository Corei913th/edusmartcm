<?php

namespace Database\Factories;

use App\Models\Eleve;
use App\Models\Etablissement;
use Illuminate\Database\Eloquent\Factories\Factory;

class EleveFactory extends Factory
{
    protected $model = Eleve::class;

    public function definition(): array
    {
        return [
            'etablissement_id' => Etablissement::factory(),
            'matricule' => $this->faker->unique()->bothify('STU-####-????'),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'date_naissance' => $this->faker->date('Y-m-d', '2010-12-31'),
            'sexe' => $this->faker->randomElement(['M', 'F']),
        ];
    }
}
