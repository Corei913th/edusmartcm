<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('personnel_administratif', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('utilisateur_id')->unique()->constrained('utilisateurs');
            $table->foreignUuid('etablissement_id')->constrained('etablissements');
            $table->string('fonction', 100);
            $table->date('date_prise_fonction')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('personnel_administratif');
    }
};
