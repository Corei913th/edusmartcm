<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->lexify('??'),
            'nom' => $this->faker->city(),
        ];
    }
}
