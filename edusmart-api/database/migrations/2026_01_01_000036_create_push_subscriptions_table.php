<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('push_subscriptions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->text('endpoint')->unique();
            $table->text('p256dh');
            $table->text('auth');
            $table->text('user_agent')->nullable();
            $table->dateTimeTz('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('push_subscriptions');
    }
};
