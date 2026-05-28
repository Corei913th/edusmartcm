<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('parents_tuteurs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('utilisateur_id')->unique()->constrained('utilisateurs');
            $table->binary('nom');
            $table->binary('prenom');
            $table->binary('telephone')->nullable();
            $table->string('email', 200)->nullable();
            $table->string('profession', 100)->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('parents_tuteurs');
    }
};
