<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            RegionSeeder::class,
            DepartementSeeder::class,
            NiveauSeeder::class,
            SerieSeeder::class,
            MatiereSeeder::class,
            PermissionSeeder::class,
            UtilisateurSeeder::class,
        ]);
    }
}
