<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conflits_sync', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sync_queue_id')->constrained('sync_queue');
            $table->string('ressource_type', 50);
            $table->uuid('ressource_id');
            $table->jsonb('valeur_cliente');
            $table->jsonb('valeur_serveur');
            $table->dateTimeTz('timestamp_client');
            $table->dateTimeTz('timestamp_serveur');
            $table->string('resolution', 20)->default('LWW_SERVER');
            $table->dateTimeTz('resolu_at')->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE conflits_sync ADD CONSTRAINT conflits_resolution_check CHECK (resolution IN ('LWW_CLIENT','LWW_SERVER','MANUEL'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('conflits_sync');
    }
};
