<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('GHAZI OUTDOOR');
            $table->text('store_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('operational_hours')->nullable();
            $table->unsignedInteger('denda_per_hari')->default(0);
            $table->unsignedInteger('pending_expiry_minutes')->default(15);
            $table->decimal('free_delivery_km', 8, 2)->default(3);
            $table->decimal('fee_per_km', 8, 2)->default(5000);
            $table->decimal('max_delivery_km', 8, 2)->default(20);
            $table->text('terms')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
