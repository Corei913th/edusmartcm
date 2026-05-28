<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('creneaux_horaires', function (Blueprint $table): void {
            $table->id();
            $table->smallInteger('jour');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->unique(['jour', 'heure_debut']);
        });

        DB::statement('ALTER TABLE creneaux_horaires ADD CONSTRAINT creneaux_jour_check CHECK (jour BETWEEN 1 AND 6)');
    }

    public function down(): void {
        Schema::dropIfExists('creneaux_horaires');
    }
};
