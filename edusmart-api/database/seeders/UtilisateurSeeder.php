<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class UtilisateurSeeder extends Seeder
{
    public function run(): void
    {
        Utilisateur::factory()->create([
            'role_code' => Role::SUPER_ADMIN,
            'etablissement_id' => null,
            'nom' => 'Admin',
            'prenom' => 'Super',
            'email' => 'admin@edusmart.cm',
            'est_actif' => true,
        ]);
    }
}
