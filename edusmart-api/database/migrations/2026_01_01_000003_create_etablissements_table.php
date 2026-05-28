<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_uai', 20)->unique();
            $table->string('nom', 200);
            $table->string('type', 50);
            $table->foreignId('departement_id')->constrained('departements');
            $table->text('adresse')->nullable();
            $table->binary('telephone')->nullable();
            $table->string('email', 200)->nullable();
            $table->boolean('est_pilote')->default(false);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('connectivite', 20)->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
            $table->dateTimeTz('updated_at')->useCurrent();
        });

        DB::statement("ALTER TABLE etablissements ADD CONSTRAINT etablissements_type_check CHECK (type IN ('LYCEE','CES','COLLEGE'))");
        DB::statement("ALTER TABLE etablissements ADD CONSTRAINT etablissements_connectivite_check CHECK (connectivite IS NULL OR connectivite IN ('3G','4G','FIBRE','ADSL','NONE'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};
