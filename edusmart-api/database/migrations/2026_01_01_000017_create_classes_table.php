<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('classes', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('etablissement_id')->constrained('etablissements');
            $table->foreignId('annee_id')->constrained('annees_scolaires');
            $table->foreignId('niveau_id')->constrained('niveaux');
            $table->foreignId('serie_id')->nullable()->constrained('series');
            $table->string('nom', 30);
            $table->smallInteger('effectif_max')->default(60);
            $table->foreignUuid('salle_id')->nullable()->constrained('salles');
            $table->dateTimeTz('created_at')->useCurrent();
            $table->unique(['etablissement_id', 'annee_id', 'nom']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('classes');
    }
};
