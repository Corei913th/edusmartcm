<?php

namespace Database\Factories;

use App\Models\Serie;
use Illuminate\Database\Eloquent\Factories\Factory;

class SerieFactory extends Factory {
    protected $model = Serie::class;

    public function definition(): array {
        return [
            'code' => $this->faker->unique()->lexify('??'),
            'libelle' => $this->faker->word(),
        ];
    }
}
