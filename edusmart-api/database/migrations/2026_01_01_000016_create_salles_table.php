<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('etablissement_id')->constrained('etablissements');
            $table->string('nom', 50);
            $table->smallInteger('capacite')->nullable();
            $table->string('type', 30)->nullable();
            $table->unique(['etablissement_id', 'nom']);
        });

        DB::statement("ALTER TABLE salles ADD CONSTRAINT salles_type_check CHECK (type IS NULL OR type IN ('CLASSE','LABO','AMPHI','SALLE_INFO'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('salles');
    }
};
