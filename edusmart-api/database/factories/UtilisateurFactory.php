<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Etablissement;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    public function definition(): array
    {
        return [
            'role_code' => Role::ENSEIGNANT,
            'etablissement_id' => Etablissement::factory(),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password_hash' => Hash::make('password', ['rounds' => 12]),
            'est_actif' => true,
        ];
    }
}
