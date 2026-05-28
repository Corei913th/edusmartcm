<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('eleve_id')->constrained('eleves');
            $table->foreignUuid('classe_id')->constrained('classes');
            $table->foreignId('annee_id')->constrained('annees_scolaires');
            $table->date('date_inscription')->useCurrent();
            $table->string('statut', 20)->default('ACTIF');
            $table->dateTimeTz('created_at')->useCurrent();
            $table->unique(['eleve_id', 'annee_id']);

            $table->index(['eleve_id', 'annee_id'], 'idx_inscriptions_eleve');
            $table->index(['classe_id', 'annee_id'], 'idx_inscriptions_classe');
        });

        DB::statement("ALTER TABLE inscriptions ADD CONSTRAINT inscriptions_statut_check CHECK (statut IN ('ACTIF','TRANSFERE','RADIE','DIPLOME'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
