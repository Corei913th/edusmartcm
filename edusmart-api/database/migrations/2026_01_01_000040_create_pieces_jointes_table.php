<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('message_id')->constrained('messages')->onDelete('cascade');
            $table->string('nom_fichier', 255);
            $table->string('type_mime', 100);
            $table->integer('taille_octets');
            $table->text('chemin_stockage');
            $table->dateTimeTz('created_at')->useCurrent();
        });

        DB::statement('ALTER TABLE pieces_jointes ADD CONSTRAINT pieces_jointes_taille_check CHECK (taille_octets <= 2097152)');
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes');
    }
};
