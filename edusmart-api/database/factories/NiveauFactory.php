<?php

namespace Database\Factories;

use App\Models\Niveau;
use Illuminate\Database\Eloquent\Factories\Factory;

class NiveauFactory extends Factory {
    protected $model = Niveau::class;

    public function definition(): array {
        return [
            'code' => $this->faker->unique()->randomElement(['6eme', '5eme', '4eme', '3eme', '2nde', '1ere', 'Tle']),
            'libelle' => $this->faker->word(),
            'ordre' => $this->faker->numberBetween(1, 7),
        ];
    }
}
