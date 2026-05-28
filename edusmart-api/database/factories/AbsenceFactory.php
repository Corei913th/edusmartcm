<?php

namespace Database\Factories;

use App\Enums\StatutAbsence;
use App\Models\Absence;
use App\Models\Inscription;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsenceFactory extends Factory
{
    protected $model = Absence::class;

    public function definition(): array
    {
        return [
            'inscription_id' => Inscription::factory(),
            'date_absence' => $this->faker->date(),
            'statut' => $this->faker->randomElement(StatutAbsence::cases())->value,
            'saisie_hors_ligne' => false,
            'created_by' => Utilisateur::factory(),
        ];
    }
}
