<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('moyennes_matieres', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('inscription_id')->constrained('inscriptions');
            $table->foreignUuid('affectation_id')->constrained('affectations_enseignement');
            $table->foreignId('periode_id')->constrained('periodes');
            $table->decimal('moyenne', 5, 2)->nullable();
            $table->smallInteger('rang_matiere')->nullable();
            $table->string('appreciation', 30)->nullable();
            $table->dateTimeTz('calculated_at')->useCurrent();
            $table->unique(['inscription_id', 'affectation_id', 'periode_id']);
        });

        DB::statement("ALTER TABLE moyennes_matieres ADD CONSTRAINT moyennes_appreciation_check CHECK (appreciation IS NULL OR appreciation IN ('TRES_BIEN','BIEN','ASSEZ_BIEN','PASSABLE','MEDIOCRE','INSUFFISANT'))");
    }

    public function down(): void {
        Schema::dropIfExists('moyennes_matieres');
    }
};
