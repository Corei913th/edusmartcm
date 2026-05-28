<?php

namespace Database\Factories;

use App\Models\Matiere;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatiereFactory extends Factory {
    protected $model = Matiere::class;

    public function definition(): array {
        return [
            'code' => $this->faker->unique()->bothify('???-###'),
            'nom' => $this->faker->randomElement(['Mathématiques', 'Français', 'Anglais', 'Physique-Chimie', 'SVT', 'Histoire', 'Géographie', 'EPS', 'Philosophie']),
            'coefficient_defaut' => $this->faker->numberBetween(1, 5),
            'type' => $this->faker->randomElement(['GENERALE', 'TECHNIQUE', 'EPS', 'OPTION']),
        ];
    }
}
