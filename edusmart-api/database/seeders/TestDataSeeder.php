<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\StatutAbsence;
use App\Enums\TypeEvaluation;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder pour créer des données de test pour Postman
 */
class TestDataSeeder extends Seeder {
    /**
     * Exécute le seeder
     */
    public function run(): void {
        // Créer les tables minimales pour les tests
        $this->createTables();

        // Créer un utilisateur de test
        $user = $this->createTestUser();

        // Créer des données de référence
        $this->createReferenceData();

        // Créer des notes et absences de test
        $this->createTestData();

        $this->command->info('Données de test créées avec succès !');
        $this->command->info('Utilisateur de test : test@edusmartcm.com / password');
        $this->command->info('Token API : Utilisez POST /api/login pour obtenir un token');
    }

    /**
     * Crée les tables minimales nécessaires
     */
    private function createTables(): void {
        // Table utilisateurs
        DB::statement('CREATE TABLE IF NOT EXISTS utilisateurs (
            id TEXT PRIMARY KEY,
            nom TEXT NOT NULL,
            prenom TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL,
            created_at DATETIME,
            updated_at DATETIME
        )');

        // Table inscriptions
        DB::statement('CREATE TABLE IF NOT EXISTS inscriptions (
            id TEXT PRIMARY KEY,
            eleve_id TEXT,
            created_at DATETIME,
            updated_at DATETIME
        )');

        // Table affectations_enseignement
        DB::statement('CREATE TABLE IF NOT EXISTS affectations_enseignement (
            id TEXT PRIMARY KEY,
            matiere_id TEXT,
            created_at DATETIME,
            updated_at DATETIME
        )');

        // Table periodes
        DB::statement('CREATE TABLE IF NOT EXISTS periodes (
            id TEXT PRIMARY KEY,
            nom TEXT NOT NULL,
            numero INTEGER,
            created_at DATETIME,
            updated_at DATETIME
        )');

        // Table notes
        DB::statement('CREATE TABLE IF NOT EXISTS notes (
            id TEXT PRIMARY KEY,
            inscription_id TEXT NOT NULL,
            affectation_id TEXT NOT NULL,
            periode_id TEXT NOT NULL,
            type_evaluation TEXT NOT NULL,
            note DECIMAL(4,2) NOT NULL,
            coefficient INTEGER NOT NULL,
            date_evaluation DATE NOT NULL,
            saisie_hors_ligne BOOLEAN DEFAULT FALSE,
            sync_at DATETIME,
            created_by TEXT NOT NULL,
            created_at DATETIME,
            updated_at DATETIME
        )');

        // Table absences
        DB::statement('CREATE TABLE IF NOT EXISTS absences (
            id TEXT PRIMARY KEY,
            inscription_id TEXT NOT NULL,
            affectation_id TEXT NOT NULL,
            date_absence DATE NOT NULL,
            heure_debut TIME NOT NULL,
            heure_fin TIME NOT NULL,
            duree_heures INTEGER NOT NULL,
            motif TEXT,
            statut TEXT NOT NULL,
            justificatif_path TEXT,
            saisie_hors_ligne BOOLEAN DEFAULT FALSE,
            sync_at DATETIME,
            created_by TEXT NOT NULL,
            created_at DATETIME,
            updated_at DATETIME
        )');

        // Table personal_access_tokens pour Sanctum
        DB::statement('CREATE TABLE IF NOT EXISTS personal_access_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tokenable_type TEXT NOT NULL,
            tokenable_id TEXT NOT NULL,
            name TEXT NOT NULL,
            token TEXT UNIQUE NOT NULL,
            abilities TEXT,
            last_used_at DATETIME,
            expires_at DATETIME,
            created_at DATETIME,
            updated_at DATETIME
        )');
    }

    /**
     * Crée un utilisateur de test
     */
    private function createTestUser(): string {
        $userId = Str::uuid()->toString();

        DB::table('utilisateurs')->insert([
            'id' => $userId,
            'nom' => 'Test',
            'prenom' => 'User',
            'email' => 'test@edusmartcm.com',
            'password' => Hash::make('password'),
            'role' => Role::ENSEIGNANT->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $userId;
    }

    /**
     * Crée des données de référence
     */
    private function createReferenceData(): void {
        // Inscriptions
        $inscriptionIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $id = Str::uuid()->toString();
            $inscriptionIds[] = $id;

            DB::table('inscriptions')->insert([
                'id' => $id,
                'eleve_id' => Str::uuid()->toString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Affectations d'enseignement
        $affectationIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $id = Str::uuid()->toString();
            $affectationIds[] = $id;

            DB::table('affectations_enseignement')->insert([
                'id' => $id,
                'matiere_id' => Str::uuid()->toString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Périodes
        $periodeIds = [];
        $periodes = ['1er Trimestre', '2ème Trimestre', '3ème Trimestre'];
        foreach ($periodes as $index => $nom) {
            $id = Str::uuid()->toString();
            $periodeIds[] = $id;

            DB::table('periodes')->insert([
                'id' => $id,
                'nom' => $nom,
                'numero' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Stocker les IDs pour les utiliser dans createTestData
        cache(['test_inscription_ids' => $inscriptionIds]);
        cache(['test_affectation_ids' => $affectationIds]);
        cache(['test_periode_ids' => $periodeIds]);
    }

    /**
     * Crée des notes et absences de test
     */
    private function createTestData(): void {
        $userId = DB::table('utilisateurs')->where('email', 'test@edusmartcm.com')->value('id');
        $inscriptionIds = cache('test_inscription_ids');
        $affectationIds = cache('test_affectation_ids');
        $periodeIds = cache('test_periode_ids');

        // Créer des notes de test
        $typeEvaluations = [TypeEvaluation::DEVOIR, TypeEvaluation::COMPOSITION, TypeEvaluation::ORAL];

        for ($i = 0; $i < 5; $i++) {
            DB::table('notes')->insert([
                'id' => Str::uuid()->toString(),
                'inscription_id' => $inscriptionIds[array_rand($inscriptionIds)],
                'affectation_id' => $affectationIds[array_rand($affectationIds)],
                'periode_id' => $periodeIds[array_rand($periodeIds)],
                'type_evaluation' => $typeEvaluations[array_rand($typeEvaluations)]->value,
                'note' => rand(8, 20) + (rand(0, 99) / 100),
                'coefficient' => rand(1, 4),
                'date_evaluation' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                'saisie_hors_ligne' => false,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Créer des absences de test
        $statuts = [StatutAbsence::JUSTIFIEE, StatutAbsence::INJUSTIFIEE, StatutAbsence::EN_ATTENTE];

        for ($i = 0; $i < 5; $i++) {
            DB::table('absences')->insert([
                'id' => Str::uuid()->toString(),
                'inscription_id' => $inscriptionIds[array_rand($inscriptionIds)],
                'affectation_id' => $affectationIds[array_rand($affectationIds)],
                'date_absence' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                'heure_debut' => '08:00',
                'heure_fin' => '10:00',
                'duree_heures' => 2,
                'motif' => 'Motif de test ' . ($i + 1),
                'statut' => $statuts[array_rand($statuts)]->value,
                'justificatif_path' => null,
                'saisie_hors_ligne' => false,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
