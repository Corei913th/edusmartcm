<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('annees_scolaires', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 20)->unique();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->boolean('est_active')->default(false);
        });

        DB::statement('ALTER TABLE annees_scolaires ADD CONSTRAINT chk_annee_dates CHECK (date_fin > date_debut)');
    }

    public function down(): void
    {
        Schema::dropIfExists('annees_scolaires');
    }
};
