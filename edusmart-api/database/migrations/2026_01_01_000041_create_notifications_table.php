<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('destinataire_id')->constrained('utilisateurs');
            $table->string('type_evenement', 50);
            $table->string('titre', 200);
            $table->text('corps');
            $table->jsonb('donnees_meta')->nullable();
            $table->boolean('lu')->default(false);
            $table->dateTimeTz('lu_at')->nullable();
            $table->dateTimeTz('created_at')->useCurrent();

            $table->index(['destinataire_id', 'lu', 'created_at'], 'idx_notifications_dest');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
