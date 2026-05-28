<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUuid('utilisateur_id')->nullable()->constrained('utilisateurs');
            $table->string('action', 100);
            $table->string('ressource_type', 50)->nullable();
            $table->uuid('ressource_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->dateTimeTz('created_at')->useCurrent();

            $table->index(['utilisateur_id', 'created_at'], 'idx_audit_utilisateur');
            $table->index(['action', 'created_at'], 'idx_audit_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
