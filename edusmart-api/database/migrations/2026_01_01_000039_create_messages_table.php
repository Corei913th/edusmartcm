<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fil_id')->constrained('fils_discussion')->onDelete('cascade');
            $table->foreignUuid('expediteur_id')->constrained('utilisateurs');
            $table->binary('contenu');
            $table->boolean('est_archive')->default(false);
            $table->dateTimeTz('created_at')->useCurrent();

            $table->index(['fil_id', 'created_at'], 'idx_messages_fil');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
