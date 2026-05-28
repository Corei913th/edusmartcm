<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('transferts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('inscription_id')->constrained('inscriptions');
            $table->foreignUuid('etablissement_origine_id')->constrained('etablissements');
            $table->foreignUuid('etablissement_destination_id')->constrained('etablissements');
            $table->date('date_transfert');
            $table->text('motif')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('utilisateurs');
            $table->dateTimeTz('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('transferts');
    }
};
