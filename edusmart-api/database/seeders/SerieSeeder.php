<?php

namespace Database\Seeders;

use App\Models\Serie;
use Illuminate\Database\Seeder;

class SerieSeeder extends Seeder {
    public function run(): void {
        if (Serie::count() > 0) {
            return;
        }

        $series = [
            ['code' => 'A',  'libelle' => 'Série A (Lettres)'],
            ['code' => 'C',  'libelle' => 'Série C (Mathématiques)'],
            ['code' => 'D',  'libelle' => 'Série D (Sciences)'],
            ['code' => 'TI', 'libelle' => 'Série TI (TIC)'],
            ['code' => 'ES', 'libelle' => 'Série ES (Économique)'],
            ['code' => 'ST', 'libelle' => 'Série ST (Sciences et Technologies)'],
        ];

        Serie::factory()
            ->count(count($series))
            ->sequence(fn ($seq) => $series[$seq->index])
            ->create();
    }
}
