<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('eleves', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('utilisateur_id')->unique()->nullable()->constrained('utilisateurs');
            $table->foreignUuid('etablissement_id')->constrained('etablissements');
            $table->string('matricule', 30)->unique();
            $table->binary('nom');
            $table->binary('prenom');
            $table->date('date_naissance')->nullable();
            $table->binary('lieu_naissance')->nullable();
            $table->string('sexe', 1)->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
            $table->dateTimeTz('updated_at')->useCurrent();
        });

        DB::statement("ALTER TABLE eleves ADD CONSTRAINT eleves_sexe_check CHECK (sexe IS NULL OR sexe IN ('M','F'))");
    }

    public function down(): void {
        Schema::dropIfExists('eleves');
    }
};
