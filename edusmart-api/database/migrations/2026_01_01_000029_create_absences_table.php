<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inscription_id')->constrained('inscriptions');
            $table->foreignUuid('affectation_id')->nullable()->constrained('affectations_enseignement');
            $table->date('date_absence');
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->smallInteger('duree_heures')->nullable();
            $table->text('motif')->nullable();
            $table->string('statut', 20)->default('INJUSTIFIEE');
            $table->text('justificatif_path')->nullable();
            $table->boolean('saisie_hors_ligne')->default(false);
            $table->dateTimeTz('sync_at')->nullable();
            $table->foreignUuid('created_by')->constrained('utilisateurs');
            $table->dateTimeTz('created_at')->useCurrent();
            $table->dateTimeTz('updated_at')->useCurrent();

            $table->index(['inscription_id', 'date_absence'], 'idx_absences_inscription');
        });

        DB::statement("ALTER TABLE absences ADD CONSTRAINT absences_statut_check CHECK (statut IN ('JUSTIFIEE','INJUSTIFIEE','EN_ATTENTE'))");
        DB::statement('CREATE INDEX idx_absences_sync ON absences(saisie_hors_ligne, sync_at) WHERE saisie_hors_ligne = TRUE');
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
