<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('preferences_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->constrained('parents_tuteurs');
            $table->string('type_evenement', 50);
            $table->boolean('canal_sms')->default(true);
            $table->boolean('canal_email')->default(true);
            $table->boolean('canal_push')->default(true);
            $table->unique(['parent_id', 'type_evenement']);
        });

        DB::statement("ALTER TABLE preferences_notifications ADD CONSTRAINT preferences_type_evenement_check CHECK (type_evenement IN ('NOUVEAU_BULLETIN','ABSENCE_INJUSTIFIEE','NOTE_DISPONIBLE','REUNION_PARENTS','MESSAGE_RECU','ALERTE_SECURITE'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('preferences_notifications');
    }
};
