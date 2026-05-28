<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('appreciations_comportementales', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('inscription_id')->constrained('inscriptions');
            $table->foreignId('periode_id')->constrained('periodes');
            $table->string('discipline', 30)->nullable();
            $table->string('ponctualite', 30)->nullable();
            $table->string('travail', 30)->nullable();
            $table->text('commentaire')->nullable();
            $table->foreignUuid('created_by')->constrained('utilisateurs');
            $table->dateTimeTz('created_at')->useCurrent();
            $table->unique(['inscription_id', 'periode_id']);
        });

        DB::statement("ALTER TABLE appreciations_comportementales ADD CONSTRAINT appreciations_discipline_check CHECK (discipline IS NULL OR discipline IN ('EXCELLENT','BIEN','MOYEN','FAIBLE'))");
        DB::statement("ALTER TABLE appreciations_comportementales ADD CONSTRAINT appreciations_ponctualite_check CHECK (ponctualite IS NULL OR ponctualite IN ('EXCELLENT','BIEN','MOYEN','FAIBLE'))");
        DB::statement("ALTER TABLE appreciations_comportementales ADD CONSTRAINT appreciations_travail_check CHECK (travail IS NULL OR travail IN ('EXCELLENT','BIEN','MOYEN','FAIBLE'))");
    }

    public function down(): void {
        Schema::dropIfExists('appreciations_comportementales');
    }
};
