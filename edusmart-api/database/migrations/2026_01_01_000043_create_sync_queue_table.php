<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('sync_queue', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('utilisateur_id')->constrained('utilisateurs');
            $table->string('methode', 10);
            $table->string('endpoint', 200);
            $table->jsonb('payload');
            $table->smallInteger('tentatives')->default(0);
            $table->string('statut', 20)->default('EN_ATTENTE');
            $table->dateTimeTz('timestamp_client');
            $table->dateTimeTz('sync_at')->nullable();
            $table->text('erreur')->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE sync_queue ADD CONSTRAINT sync_queue_methode_check CHECK (methode IN ('POST','PUT','PATCH','DELETE'))");
        DB::statement("ALTER TABLE sync_queue ADD CONSTRAINT sync_queue_statut_check CHECK (statut IN ('EN_ATTENTE','EN_COURS','SYNCHRONISE','CONFLIT','ECHEC'))");
        DB::statement("CREATE INDEX idx_sync_queue_pending ON sync_queue(statut, tentatives) WHERE statut = 'EN_ATTENTE'");
    }

    public function down(): void {
        Schema::dropIfExists('sync_queue');
    }
};
