<?php

namespace Database\Factories;

use App\Models\Departement;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartementFactory extends Factory
{
    protected $model = Departement::class;

    public function definition(): array
    {
        return [
            'region_id' => Region::factory(),
            'code' => $this->faker->unique()->lexify('???'),
            'nom' => $this->faker->city(),
        ];
    }
}
