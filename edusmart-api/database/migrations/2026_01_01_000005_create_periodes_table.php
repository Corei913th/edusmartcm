<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('periodes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('annee_id')->constrained('annees_scolaires');
            $table->smallInteger('numero');
            $table->string('libelle', 30);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->unique(['annee_id', 'numero']);
        });

        DB::statement('ALTER TABLE periodes ADD CONSTRAINT periodes_numero_check CHECK (numero BETWEEN 1 AND 3)');
    }

    public function down(): void {
        Schema::dropIfExists('periodes');
    }
};
