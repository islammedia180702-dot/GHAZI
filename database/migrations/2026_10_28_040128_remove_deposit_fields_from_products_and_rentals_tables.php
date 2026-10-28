<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('rentals')
            ->join('payments', 'payments.rental_id', '=', 'rentals.id')
            ->where('payments.status', 'pending')
            ->where('rentals.deposit_total', '>', 0)
            ->select('payments.id as payment_id', 'payments.amount', 'rentals.deposit_total')
            ->orderBy('payments.id')
            ->chunkById(100, function ($payments): void {
                foreach ($payments as $payment) {
                    DB::table('payments')
                        ->where('id', $payment->payment_id)
                        ->where('status', 'pending')
                        ->update([
                            'amount' => max(0, (int) $payment->amount - (int) $payment->deposit_total),
                        ]);
                }
            }, 'payments.id', 'payment_id');

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('deposit');
        });

        Schema::table('rentals', function (Blueprint $table): void {
            $table->dropColumn('deposit_total');
        });
    }

    /**
     * Restore the columns with zero values. Removed deposit amounts and pending-payment adjustments cannot be reconstructed.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->unsignedInteger('deposit')->default(0);
        });

        Schema::table('rentals', function (Blueprint $table): void {
            $table->unsignedInteger('deposit_total')->default(0);
        });
    }
};
