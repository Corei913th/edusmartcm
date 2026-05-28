<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('progressions_cours', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('affectation_id')->constrained('affectations_enseignement');
            $table->foreignId('periode_id')->constrained('periodes');
            $table->string('chapitre', 200);
            $table->text('objectif')->nullable();
            $table->string('statut', 20)->default('PLANIFIE');
            $table->date('date_debut_prevu')->nullable();
            $table->date('date_fin_prevu')->nullable();
            $table->date('date_fin_reel')->nullable();
            $table->smallInteger('taux_avancement')->default(0);
            $table->dateTimeTz('created_at')->useCurrent();
            $table->dateTimeTz('updated_at')->useCurrent();
        });

        DB::statement("ALTER TABLE progressions_cours ADD CONSTRAINT progressions_statut_check CHECK (statut IN ('PLANIFIE','EN_COURS','TERMINE'))");
        DB::statement('ALTER TABLE progressions_cours ADD CONSTRAINT progressions_taux_check CHECK (taux_avancement BETWEEN 0 AND 100)');
    }

    public function down(): void {
        Schema::dropIfExists('progressions_cours');
    }
};
