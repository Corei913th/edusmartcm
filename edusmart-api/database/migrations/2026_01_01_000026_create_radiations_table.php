<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('radiations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('inscription_id')->constrained('inscriptions');
            $table->date('date_radiation');
            $table->string('motif', 50)->nullable();
            $table->text('observations')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('utilisateurs');
            $table->dateTimeTz('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE radiations ADD CONSTRAINT radiations_motif_check CHECK (motif IS NULL OR motif IN ('EXCLUSION','ABANDON','DECES','AUTRE'))");
    }

    public function down(): void {
        Schema::dropIfExists('radiations');
    }
};
