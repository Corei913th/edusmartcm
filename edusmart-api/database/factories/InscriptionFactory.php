<?php

namespace Database\Factories;

use App\Enums\StatutInscription;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class InscriptionFactory extends Factory
{
    protected $model = Inscription::class;

    public function definition(): array
    {
        return [
            'eleve_id'         => Eleve::factory(),
            'classe_id'        => Classe::factory(),
            'annee_id'         => AnneeScolaire::factory(),
            'date_inscription' => $this->faker->date(),
            'statut'           => StatutInscription::ACTIF,
        ];
    }
}
