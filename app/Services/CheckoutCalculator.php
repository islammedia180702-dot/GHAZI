<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;

class CheckoutCalculator
{
    public function __construct(protected RentalAvailability $rentalAvailability) {}

    /**
     * Build line items for a cart, validating availability for the rental dates.
     *
     * @return array<int, array{product_id: int, quantity: int, unit_price: int, days: int, subtotal: int}>
     */
    public function lineItems(
        array $cart,
        int $days,
        Carbon $startDate,
        Carbon $endDate,
        ?string &$errorField = null,
        ?string &$errorMessage = null
    ): array {
        $lineItems = [];
        $products = Product::query()
            ->whereKey(array_keys($cart))
            ->get()
            ->keyBy('id');

        foreach ($cart as $productId => $item) {
            $product = $products->get((int) $productId);

            if (! $product || ! $product->is_active) {
                continue;
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $availableUnits = $this->rentalAvailability->availableUnits($product, $startDate, $endDate);

            if ($quantity > $availableUnits) {
                $errorField = 'quantity';
                $errorMessage = $product->name.' hanya tersedia '.$availableUnits.' unit pada tanggal yang Anda pilih.';

                return [];
            }

            $lineTotal = $product->price_per_day * $quantity * $days;

            $lineItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price_per_day,
                'days' => $days,
                'subtotal' => $lineTotal,
            ];
        }

        return $lineItems;
    }

    /**
     * Calculate the rental days between start and end date (inclusive).
     */
    public function daysBetween(Carbon $startDate, Carbon $endDate): int
    {
        return max(1, $startDate->diffInDays($endDate) + 1);
    }
}
