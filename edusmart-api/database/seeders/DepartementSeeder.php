<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder {
    public function run(): void {
        if (Departement::count() > 0) {
            return;
        }

        $departements = [
            ['region_id' => 1, 'code' => 'MF', 'nom' => 'Mfoundi'],
            ['region_id' => 1, 'code' => 'LE', 'nom' => 'Lekié'],
            ['region_id' => 2, 'code' => 'WU', 'nom' => 'Wouri'],
            ['region_id' => 2, 'code' => 'NK', 'nom' => 'Nkam'],
            ['region_id' => 3, 'code' => 'BE', 'nom' => 'Bénoué'],
            ['region_id' => 3, 'code' => 'FR', 'nom' => 'Faro'],
            ['region_id' => 4, 'code' => 'LM', 'nom' => 'Lom-et-Djérem'],
            ['region_id' => 4, 'code' => 'KT', 'nom' => 'Kadey'],
            ['region_id' => 5, 'code' => 'MN', 'nom' => 'Ménoua'],
            ['region_id' => 5, 'code' => 'BD', 'nom' => 'Bamboutos'],
            ['region_id' => 6, 'code' => 'MV', 'nom' => 'Mvila'],
            ['region_id' => 6, 'code' => 'OC', 'nom' => 'Océan'],
            ['region_id' => 7, 'code' => 'VI', 'nom' => 'Vina'],
            ['region_id' => 7, 'code' => 'MJ', 'nom' => 'Mayo-Banyo'],
            ['region_id' => 8, 'code' => 'LO', 'nom' => 'Logone-et-Chari'],
            ['region_id' => 8, 'code' => 'MA', 'nom' => 'Mayo-Danay'],
            ['region_id' => 9, 'code' => 'BO', 'nom' => 'Boyo'],
            ['region_id' => 9, 'code' => 'BU', 'nom' => 'Bui'],
            ['region_id' => 10, 'code' => 'FA', 'nom' => 'Fako'],
            ['region_id' => 10, 'code' => 'ME', 'nom' => 'Meme'],
        ];

        Departement::factory()
            ->count(count($departements))
            ->sequence(fn ($seq) => $departements[$seq->index])
            ->create();
    }
}
