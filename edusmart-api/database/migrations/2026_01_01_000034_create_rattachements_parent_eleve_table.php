<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('rattachements_parent_eleve', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->constrained('parents_tuteurs');
            $table->foreignUuid('eleve_id')->constrained('eleves');
            $table->string('lien', 30);
            $table->boolean('est_contact_principal')->default(false);
            $table->boolean('peut_consulter_notes')->default(true);
            $table->boolean('peut_recevoir_notifs')->default(true);
            $table->dateTimeTz('created_at')->useCurrent();
            $table->unique(['parent_id', 'eleve_id']);

            $table->index('parent_id', 'idx_rattachements_parent');
            $table->index('eleve_id', 'idx_rattachements_eleve');
        });

        DB::statement("ALTER TABLE rattachements_parent_eleve ADD CONSTRAINT rattachements_lien_check CHECK (lien IN ('PERE','MERE','TUTEUR','AUTRE'))");
    }

    public function down(): void {
        Schema::dropIfExists('rattachements_parent_eleve');
    }
};
