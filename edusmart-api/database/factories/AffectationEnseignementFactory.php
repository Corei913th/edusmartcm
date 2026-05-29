<?php

namespace Database\Factories;

use App\Models\AffectationEnseignement;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Database\Eloquent\Factories\Factory;

class AffectationEnseignementFactory extends Factory
{
    protected $model = AffectationEnseignement::class;

    public function definition(): array
    {
        return [
            'enseignant_id' => Enseignant::factory(),
            'classe_id'     => Classe::factory(),
            'matiere_id'    => Matiere::factory(),
            'annee_id'      => AnneeScolaire::factory(),
            'coefficient'   => $this->faker->numberBetween(1, 4),
        ];
    }
}
