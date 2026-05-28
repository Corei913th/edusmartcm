<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('envois_notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->string('canal', 20);
            $table->string('statut', 20)->default('EN_ATTENTE');
            $table->smallInteger('tentatives')->default(0);
            $table->dateTimeTz('derniere_tentative')->nullable();
            $table->text('erreur')->nullable();
            $table->dateTimeTz('envoye_at')->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE envois_notifications ADD CONSTRAINT envois_canal_check CHECK (canal IN ('SMS','EMAIL','PUSH','INAPP'))");
        DB::statement("ALTER TABLE envois_notifications ADD CONSTRAINT envois_statut_check CHECK (statut IN ('EN_ATTENTE','ENVOYE','ECHEC','IGNORE'))");
        DB::statement("CREATE INDEX idx_envois_statut ON envois_notifications(statut, created_at) WHERE statut IN ('EN_ATTENTE', 'ECHEC')");
    }

    public function down(): void {
        Schema::dropIfExists('envois_notifications');
    }
};
