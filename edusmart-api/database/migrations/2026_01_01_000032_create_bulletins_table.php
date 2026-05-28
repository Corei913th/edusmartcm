<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('bulletins', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('inscription_id')->constrained('inscriptions');
            $table->foreignId('periode_id')->constrained('periodes');
            $table->smallInteger('rang_classe')->nullable();
            $table->decimal('moyenne_generale', 5, 2)->nullable();
            $table->text('appreciation_generale')->nullable();
            $table->text('pdf_path')->nullable();
            $table->dateTimeTz('pdf_genere_at')->nullable();
            $table->boolean('est_publie')->default(false);
            $table->dateTimeTz('publie_at')->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
            $table->unique(['inscription_id', 'periode_id']);
        });

        DB::statement('CREATE INDEX idx_bulletins_publie ON bulletins(est_publie, publie_at) WHERE est_publie = TRUE');
    }

    public function down(): void {
        Schema::dropIfExists('bulletins');
    }
};
