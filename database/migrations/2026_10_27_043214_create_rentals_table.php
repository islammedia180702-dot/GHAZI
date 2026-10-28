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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending');
            $table->enum('pickup_method', ['pickup', 'delivery'])->default('pickup');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('deposit_total')->default(0);
            $table->unsignedInteger('delivery_fee')->default(0);
            $table->unsignedInteger('grand_total')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
