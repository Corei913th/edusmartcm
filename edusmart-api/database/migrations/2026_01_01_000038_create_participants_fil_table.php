<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('participants_fil', function (Blueprint $table) {
            $table->foreignUuid('fil_id')->constrained('fils_discussion')->onDelete('cascade');
            $table->foreignUuid('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->dateTimeTz('lu_at')->nullable();
            $table->primary(['fil_id', 'utilisateur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants_fil');
    }
};
