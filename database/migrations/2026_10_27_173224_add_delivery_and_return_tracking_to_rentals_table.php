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
        Schema::table('rentals', function (Blueprint $table) {
            $table->decimal('delivery_distance_km', 8, 2)->nullable();
            $table->unsignedSmallInteger('late_days')->default(0);
            $table->unsignedInteger('late_fee_total')->default(0);
            $table->timestamp('returned_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_distance_km',
                'late_days',
                'late_fee_total',
                'returned_at',
            ]);
        });
    }
};
