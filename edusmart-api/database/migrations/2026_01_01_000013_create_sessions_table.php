<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->string('token_hash', 255);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTimeTz('expire_at');
            $table->boolean('revoque')->default(false);
            $table->dateTimeTz('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
