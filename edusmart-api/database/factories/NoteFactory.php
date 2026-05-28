<?php

namespace Database\Factories;

use App\Enums\TypeEvaluation;
use App\Models\AffectationEnseignement;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\Periode;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition(): array
    {
        return [
            'inscription_id' => Inscription::factory(),
            'affectation_id' => AffectationEnseignement::factory(),
            'periode_id' => Periode::factory(),
            'type_evaluation' => $this->faker->randomElement(TypeEvaluation::cases())->value,
            'note' => $this->faker->randomFloat(2, 0, 20),
            'coefficient' => $this->faker->numberBetween(1, 3),
            'date_evaluation' => $this->faker->date(),
            'saisie_hors_ligne' => false,
            'created_by' => Utilisateur::factory(),
        ];
    }
}
