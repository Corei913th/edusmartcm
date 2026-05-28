<?php

namespace Database\Factories;

use App\Enums\Connectivite;
use App\Enums\EtablissementType;
use App\Models\Departement;
use App\Models\Etablissement;
use Illuminate\Database\Eloquent\Factories\Factory;

class EtablissementFactory extends Factory {
    protected $model = Etablissement::class;

    public function definition(): array {
        return [
            'code_uai' => $this->faker->unique()->bothify('???-####'),
            'nom' => $this->faker->company(),
            'type' => $this->faker->randomElement(EtablissementType::cases())->value,
            'departement_id' => Departement::factory(),
            'adresse' => $this->faker->address(),
            'email' => $this->faker->email(),
            'est_pilote' => $this->faker->boolean(10),
            'latitude' => $this->faker->latitude(2, 13),
            'longitude' => $this->faker->longitude(8, 16),
            'connectivite' => $this->faker->randomElement(Connectivite::cases())->value,
        ];
    }
}
