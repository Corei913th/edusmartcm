<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fils_discussion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('etablissement_id')->constrained('etablissements');
            $table->string('sujet', 200);
            $table->string('type', 30);
            $table->dateTimeTz('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE fils_discussion ADD CONSTRAINT fils_discussion_type_check CHECK (type IN ('PARENT_ENSEIGNANT','PARENT_DIRECTION','ENSEIGNANT_DIRECTION','INTERNE'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('fils_discussion');
    }
};
