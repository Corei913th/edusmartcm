<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder {
    public function run(): void {
        if (Region::count() > 0) {
            return;
        }

        $regions = [
            ['code' => 'CE', 'nom' => 'Centre'],
            ['code' => 'LT', 'nom' => 'Littoral'],
            ['code' => 'NO', 'nom' => 'Nord'],
            ['code' => 'ES', 'nom' => 'Est'],
            ['code' => 'OU', 'nom' => 'Ouest'],
            ['code' => 'SU', 'nom' => 'Sud'],
            ['code' => 'AD', 'nom' => 'Adamaoua'],
            ['code' => 'EN', 'nom' => 'Extrême-Nord'],
            ['code' => 'NW', 'nom' => 'Nord-Ouest'],
            ['code' => 'SW', 'nom' => 'Sud-Ouest'],
        ];

        Region::factory()
            ->count(count($regions))
            ->sequence(fn ($seq) => $regions[$seq->index])
            ->create();
    }
}
