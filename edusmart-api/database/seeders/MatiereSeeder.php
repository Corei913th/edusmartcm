<?php

namespace Database\Seeders;

use App\Models\Matiere;
use Illuminate\Database\Seeder;

class MatiereSeeder extends Seeder
{
    public function run(): void
    {
        $matieres = [
            ['code' => 'MAT', 'nom' => 'Mathématiques', 'coefficient_defaut' => 4, 'type' => 'GENERALE'],
            ['code' => 'PHY', 'nom' => 'Physique', 'coefficient_defaut' => 3, 'type' => 'GENERALE'],
            ['code' => 'CHI', 'nom' => 'Chimie', 'coefficient_defaut' => 2, 'type' => 'GENERALE'],
            ['code' => 'SVT', 'nom' => 'Sciences de la Vie et de la Terre', 'coefficient_defaut' => 2, 'type' => 'GENERALE'],
            ['code' => 'FRA', 'nom' => 'Français', 'coefficient_defaut' => 4, 'type' => 'GENERALE'],
            ['code' => 'ANG', 'nom' => 'Anglais', 'coefficient_defaut' => 3, 'type' => 'GENERALE'],
            ['code' => 'HIS', 'nom' => 'Histoire', 'coefficient_defaut' => 2, 'type' => 'GENERALE'],
            ['code' => 'GEO', 'nom' => 'Géographie', 'coefficient_defaut' => 2, 'type' => 'GENERALE'],
            ['code' => 'EPS', 'nom' => 'Éducation Physique et Sportive', 'coefficient_defaut' => 1, 'type' => 'EPS'],
            ['code' => 'INF', 'nom' => 'Informatique', 'coefficient_defaut' => 2, 'type' => 'TECHNIQUE'],
        ];

        Matiere::factory()
            ->count(count($matieres))
            ->sequence(fn ($seq) => $matieres[$seq->index])
            ->create();
    }
}
