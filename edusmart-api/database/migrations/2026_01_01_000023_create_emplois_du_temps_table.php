<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('emplois_du_temps', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('affectation_id')->constrained('affectations_enseignement');
            $table->foreignUuid('salle_id')->nullable()->constrained('salles');
            $table->foreignId('creneau_id')->constrained('creneaux_horaires');
            $table->foreignId('annee_id')->constrained('annees_scolaires');
            $table->unique(['affectation_id', 'creneau_id', 'annee_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('emplois_du_temps');
    }
};
