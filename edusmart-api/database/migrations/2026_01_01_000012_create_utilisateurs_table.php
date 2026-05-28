<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('role_code', 30);
            $table->foreignUuid('etablissement_id')->nullable()->constrained('etablissements');
            $table->binary('nom');
            $table->binary('prenom');
            $table->binary('telephone')->unique()->nullable();
            $table->string('email', 200)->unique()->nullable();
            $table->string('password_hash', 255)->nullable();
            $table->boolean('est_actif')->default(true);
            $table->dateTimeTz('derniere_connexion')->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
            $table->dateTimeTz('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
