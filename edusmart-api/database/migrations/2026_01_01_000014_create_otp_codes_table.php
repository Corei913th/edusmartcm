<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->string('code_hash', 255);
            $table->string('type', 20);
            $table->smallInteger('tentatives')->default(0);
            $table->dateTimeTz('expire_at');
            $table->boolean('utilise')->default(false);
            $table->dateTimeTz('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE otp_codes ADD CONSTRAINT otp_codes_type_check CHECK (type IN ('SMS','EMAIL'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
