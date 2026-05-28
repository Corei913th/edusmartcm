<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inscription_id')->constrained('inscriptions');
            $table->foreignUuid('affectation_id')->constrained('affectations_enseignement');
            $table->foreignId('periode_id')->constrained('periodes');
            $table->string('type_evaluation', 30);
            $table->decimal('note', 5, 2);
            $table->smallInteger('coefficient')->default(1);
            $table->date('date_evaluation');
            $table->boolean('saisie_hors_ligne')->default(false);
            $table->dateTimeTz('sync_at')->nullable();
            $table->foreignUuid('created_by')->constrained('utilisateurs');
            $table->dateTimeTz('created_at')->useCurrent();
            $table->dateTimeTz('updated_at')->useCurrent();

            $table->index(['inscription_id', 'periode_id'], 'idx_notes_inscription');
            $table->index(['affectation_id', 'periode_id'], 'idx_notes_affectation');
        });

        DB::statement("ALTER TABLE notes ADD CONSTRAINT notes_type_evaluation_check CHECK (type_evaluation IN ('DEVOIR','COMPOSITION','ORAL','TP','EXAMEN'))");
        DB::statement('ALTER TABLE notes ADD CONSTRAINT notes_note_check CHECK (note >= 0 AND note <= 20)');
        DB::statement('CREATE INDEX idx_notes_sync ON notes(saisie_hors_ligne, sync_at) WHERE saisie_hors_ligne = TRUE');
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
