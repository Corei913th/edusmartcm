<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enseignants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('utilisateur_id')->unique()->constrained('utilisateurs');
            $table->foreignUuid('etablissement_id')->constrained('etablissements');
            $table->string('matricule', 30)->unique()->nullable();
            $table->string('grade', 50)->nullable();
            $table->string('specialite', 100)->nullable();
            $table->date('date_prise_fonction')->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enseignants');
    }
};
