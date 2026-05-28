<?php

namespace Database\Factories;

use App\Models\Enseignant;
use App\Models\Etablissement;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnseignantFactory extends Factory {
    protected $model = Enseignant::class;

    public function definition(): array {
        return [
            'utilisateur_id' => Utilisateur::factory(),
            'etablissement_id' => Etablissement::factory(),
            'matricule' => $this->faker->unique()->bothify('TCH-####-????'),
            'grade' => $this->faker->randomElement(['PLEG', 'PCEG', 'BIEP', 'IAPE']),
            'specialite' => $this->faker->randomElement(['Mathématiques', 'Français', 'Anglais', 'PC', 'SVT', 'Histoire-Géo']),
            'date_prise_fonction' => $this->faker->date(),
        ];
    }
}
