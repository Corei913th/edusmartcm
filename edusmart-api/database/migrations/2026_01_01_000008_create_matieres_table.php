<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('matieres', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('nom', 100);
            $table->smallInteger('coefficient_defaut')->default(1);
            $table->string('type', 20)->nullable();
        });

        DB::statement("ALTER TABLE matieres ADD CONSTRAINT matieres_type_check CHECK (type IS NULL OR type IN ('GENERALE','TECHNIQUE','EPS','OPTION'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};
