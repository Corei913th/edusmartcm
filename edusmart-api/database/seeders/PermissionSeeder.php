<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder {
    public function run(): void {
        if (Permission::count() > 0) {
            return;
        }

        $permissions = [
            ['code' => 'marks:read:own',       'description' => 'Lire ses propres notes'],
            ['code' => 'marks:read:class',     'description' => 'Lire les notes de sa classe'],
            ['code' => 'marks:write:class',    'description' => 'Saisir les notes de sa classe'],
            ['code' => 'marks:export',         'description' => 'Exporter les notes'],
            ['code' => 'absences:write',       'description' => 'Saisir les absences'],
            ['code' => 'absences:read',        'description' => 'Consulter les absences'],
            ['code' => 'absences:justify',     'description' => 'Justifier une absence'],
            ['code' => 'bulletins:read',       'description' => 'Consulter les bulletins'],
            ['code' => 'bulletins:export',     'description' => 'Exporter les bulletins PDF'],
            ['code' => 'bulletins:publish',    'description' => 'Publier les bulletins'],
            ['code' => 'eleves:read',          'description' => 'Consulter la liste des élèves'],
            ['code' => 'eleves:write',         'description' => 'Gérer les inscriptions'],
            ['code' => 'users:manage',         'description' => 'Gérer les utilisateurs'],
            ['code' => 'roles:manage',         'description' => 'Gérer les rôles et permissions'],
            ['code' => 'etablissement:config', 'description' => "Configurer l'établissement"],
            ['code' => 'messages:send',        'description' => 'Envoyer des messages'],
            ['code' => 'messages:read',        'description' => 'Lire ses messages'],
            ['code' => 'emplois:read',         'description' => "Consulter l'emploi du temps"],
            ['code' => 'emplois:write',        'description' => "Gérer l'emploi du temps"],
            ['code' => 'sync:manage',          'description' => 'Gérer la synchronisation'],
        ];

        Permission::factory()
            ->count(count($permissions))
            ->sequence(fn ($seq) => $permissions[$seq->index])
            ->create();
    }
}
