<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('
            CREATE OR REPLACE FUNCTION trigger_set_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql
        ');

        $tables = ['etablissements', 'utilisateurs', 'eleves', 'notes', 'absences', 'progressions_cours'];
        foreach ($tables as $table) {
            DB::statement("
                CREATE TRIGGER set_updated_at BEFORE UPDATE ON {$table}
                FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at()
            ");
        }
    }

    public function down(): void
    {
        $tables = ['etablissements', 'utilisateurs', 'eleves', 'notes', 'absences', 'progressions_cours'];
        foreach ($tables as $table) {
            DB::statement("DROP TRIGGER IF EXISTS set_updated_at ON {$table}");
        }
        DB::statement('DROP FUNCTION IF EXISTS trigger_set_updated_at');
    }
};
