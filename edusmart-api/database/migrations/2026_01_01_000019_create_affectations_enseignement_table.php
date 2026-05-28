<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('affectations_enseignement', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('enseignant_id')->constrained('enseignants');
            $table->foreignUuid('classe_id')->constrained('classes');
            $table->foreignId('matiere_id')->constrained('matieres');
            $table->foreignId('annee_id')->constrained('annees_scolaires');
            $table->smallInteger('coefficient')->default(1);
            $table->unique(['classe_id', 'matiere_id', 'annee_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('affectations_enseignement');
    }
};
