<?php


namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder minimal pour les tests CI/CD
 */
class CiTestSeeder extends Seeder {
    /**
     * Exécute le seeder pour les tests CI
     */
    public function run(): void {
        // Créer les tables minimales pour les tests
        $this->createMinimalTables();

        // Créer un utilisateur de test
        $this->createTestUser();

        // Créer des données de référence minimales
        $this->createMinimalReferenceData();

        $this->command->info('Données de test CI créées avec succès !');
    }

    /**
     * Crée les tables minimales nécessaires pour les tests
     */
    private function createMinimalTables(): void {
        // Table utilisateurs (si elle n'existe pas déjà)
        if (!DB::getSchemaBuilder()->hasTable('utilisateurs')) {
            DB::statement('CREATE TABLE IF NOT EXISTS utilisateurs (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                nom VARCHAR(255) NOT NULL,
                prenom VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) NOT NULL,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )');
        }

        // Table inscriptions
        if (!DB::getSchemaBuilder()->hasTable('inscriptions')) {
            DB::statement('CREATE TABLE IF NOT EXISTS inscriptions (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                eleve_id UUID,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )');
        }

        // Table affectations_enseignement
        if (!DB::getSchemaBuilder()->hasTable('affectations_enseignement')) {
            DB::statement('CREATE TABLE IF NOT EXISTS affectations_enseignement (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                matiere_id UUID,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )');
        }

        // Table periodes
        if (!DB::getSchemaBuilder()->hasTable('periodes')) {
            DB::statement('CREATE TABLE IF NOT EXISTS periodes (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                nom VARCHAR(255) NOT NULL,
                numero INTEGER,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )');
        }

        // Table notes
        if (!DB::getSchemaBuilder()->hasTable('notes')) {
            DB::statement('CREATE TABLE IF NOT EXISTS notes (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                inscription_id UUID NOT NULL,
                affectation_id UUID NOT NULL,
                periode_id UUID NOT NULL,
                type_evaluation VARCHAR(50) NOT NULL,
                note DECIMAL(4,2) NOT NULL,
                coefficient INTEGER NOT NULL,
                date_evaluation DATE NOT NULL,
                saisie_hors_ligne BOOLEAN DEFAULT FALSE,
                sync_at TIMESTAMP,
                created_by UUID NOT NULL,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )');
        }

        // Table absences
        if (!DB::getSchemaBuilder()->hasTable('absences')) {
            DB::statement('CREATE TABLE IF NOT EXISTS absences (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                inscription_id UUID NOT NULL,
                affectation_id UUID NOT NULL,
                date_absence DATE NOT NULL,
                heure_debut TIME NOT NULL,
                heure_fin TIME NOT NULL,
                duree_heures INTEGER NOT NULL,
                motif TEXT,
                statut VARCHAR(50) NOT NULL,
                justificatif_path VARCHAR(255),
                saisie_hors_ligne BOOLEAN DEFAULT FALSE,
                sync_at TIMESTAMP,
                created_by UUID NOT NULL,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )');
        }

        // Table personal_access_tokens pour Sanctum
        if (!DB::getSchemaBuilder()->hasTable('personal_access_tokens')) {
            DB::statement('CREATE TABLE IF NOT EXISTS personal_access_tokens (
                id SERIAL PRIMARY KEY,
                tokenable_type VARCHAR(255) NOT NULL,
                tokenable_id UUID NOT NULL,
                name VARCHAR(255) NOT NULL,
                token VARCHAR(64) UNIQUE NOT NULL,
                abilities TEXT,
                last_used_at TIMESTAMP,
                expires_at TIMESTAMP,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )');
        }
    }

    /**
     * Crée un utilisateur de test
     */
    private function createTestUser(): void {
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
    }

    /**
     * Crée des données de référence minimales
     */
    private function createMinimalReferenceData(): void {
        // Une inscription
        $inscriptionId = Str::uuid()->toString();
        DB::table('inscriptions')->insert([
            'id' => $inscriptionId,
            'eleve_id' => Str::uuid()->toString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Une affectation d'enseignement
        $affectationId = Str::uuid()->toString();
        DB::table('affectations_enseignement')->insert([
            'id' => $affectationId,
            'matiere_id' => Str::uuid()->toString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Une période
        $periodeId = Str::uuid()->toString();
        DB::table('periodes')->insert([
            'id' => $periodeId,
            'nom' => '1er Trimestre',
            'numero' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
