<?php

namespace App\Services;

use App\Models\Product;
use App\Models\RentalItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class RentalAvailability
{
    public function availableUnits(Product $product, Carbon $startDate, Carbon $endDate): int
    {
        $reservedItems = RentalItem::query()
            ->whereBelongsTo($product, 'product')
            ->whereHas('rental', function (Builder $query) use ($startDate, $endDate): void {
                $query->whereIn('status', ['pending', 'paid', 'in_use'])
                    ->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate)
                    ->whereDoesntHave('payment', function (Builder $query): void {
                        $query->where('status', 'pending')
                            ->whereNotNull('expires_at')
                            ->where('expires_at', '<=', now());
                    });
            })
            ->with('rental:id,start_date,end_date')
            ->get();

        $dailyChanges = [];

        foreach ($reservedItems as $item) {
            $rental = $item->rental;
            $overlapStart = $rental->start_date->greaterThan($startDate)
                ? $rental->start_date
                : $startDate;
            $overlapEnd = $rental->end_date->lessThan($endDate)
                ? $rental->end_date
                : $endDate;
            $startKey = $overlapStart->toDateString();
            $endKey = $overlapEnd->copy()->addDay()->toDateString();

            $dailyChanges[$startKey] = ($dailyChanges[$startKey] ?? 0) + $item->quantity;
            $dailyChanges[$endKey] = ($dailyChanges[$endKey] ?? 0) - $item->quantity;
        }

        ksort($dailyChanges);

        $reservedUnits = 0;
        $maximumReservedUnits = 0;

        foreach ($dailyChanges as $change) {
            $reservedUnits += $change;
            $maximumReservedUnits = max($maximumReservedUnits, $reservedUnits);
        }

        return max(0, $product->stock_total - $maximumReservedUnits);
    }
}
