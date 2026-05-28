<?php

namespace Database\Seeders;

use App\Models\Niveau;
use Illuminate\Database\Seeder;

class NiveauSeeder extends Seeder {
    public function run(): void {
        if (Niveau::count() > 0) {
            return;
        }

        $niveaux = [
            ['code' => '6eme', 'libelle' => 'Sixième', 'ordre' => 1],
            ['code' => '5eme', 'libelle' => 'Cinquième', 'ordre' => 2],
            ['code' => '4eme', 'libelle' => 'Quatrième', 'ordre' => 3],
            ['code' => '3eme', 'libelle' => 'Troisième', 'ordre' => 4],
            ['code' => '2nde', 'libelle' => 'Seconde', 'ordre' => 5],
            ['code' => '1ere', 'libelle' => 'Première', 'ordre' => 6],
            ['code' => 'Tle',  'libelle' => 'Terminale', 'ordre' => 7],
        ];

        Niveau::factory()
            ->count(count($niveaux))
            ->sequence(fn ($seq) => $niveaux[$seq->index])
            ->create();
    }
}
