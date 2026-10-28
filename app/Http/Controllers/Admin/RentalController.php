<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RentalController extends Controller
{
    /**
     * Manage all rentals from the admin side.
     */
    public function index(Request $request): View
    {
        $rentals = $this->filteredRentals($request);

        return view('admin.rentals.index', [
            'rentals' => $rentals,
        ]);
    }

    /**
     * Detail view for a single rental.
     */
    public function show(Rental $rental): View
    {
        $rental->load(['items.product', 'customer', 'payment']);
        $setting = Setting::query()->first();

        return view('admin.rentals.show', [
            'rental' => $rental,
            'setting' => $setting,
        ]);
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rental_ids' => ['required', 'array', 'min:1', 'max:15'],
            'rental_ids.*' => ['required', 'integer', 'distinct', 'exists:rentals,id'],
        ]);

        $selectedIds = array_map('intval', $validated['rental_ids']);
        $visibleIds = $this->filteredRentals($request)->getCollection()->modelKeys();

        if (array_diff($selectedIds, $visibleIds) !== []) {
            return back()->withErrors([
                'rental_ids' => 'Pilih hanya pesanan yang terlihat di halaman ini.',
            ]);
        }

        $deletedCount = DB::transaction(function () use ($selectedIds): ?int {
            $rentals = Rental::query()
                ->whereKey($selectedIds)
                ->lockForUpdate()
                ->with('payment')
                ->get();

            if (
                $rentals->count() !== count($selectedIds)
                || $rentals->contains(fn (Rental $rental): bool => ! $rental->isBulkDeletable())
            ) {
                return null;
            }

            foreach ($rentals as $rental) {
                $rental->delete();
            }

            return $rentals->count();
        });

        if ($deletedCount === null) {
            return back()->withErrors([
                'rental_ids' => 'Hanya pesanan selesai, dikembalikan, dibatalkan, atau dengan pembayaran kedaluwarsa yang dapat dihapus. Muat ulang daftar dan coba lagi.',
            ]);
        }

        return back()->with('status', "{$deletedCount} pesanan beserta detail dan pembayarannya berhasil dihapus permanen.");
    }

    /**
     * Update the status of a rental (pending, paid, in_use, completed, cancelled, returned).
     */
    public function updateStatus(Request $request, Rental $rental): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:paid,in_use,cancelled'],
            'delivery_distance_km' => [
                $rental->pickup_method === 'delivery' && $request->input('status') === 'paid' ? 'required' : 'nullable',
                'nullable',
                'numeric',
                'min:0',
                'max:'.(Setting::query()->value('max_delivery_km') ?? 20),
            ],
        ]);

        abort_unless(
            ($validated['status'] === 'paid' && $rental->status === 'pending')
                || ($validated['status'] === 'in_use' && $rental->status === 'paid')
                || ($validated['status'] === 'cancelled' && in_array($rental->status, ['pending', 'paid'], true)),
            409
        );

        DB::transaction(function () use ($rental, $validated): void {
            $rental = Rental::query()->lockForUpdate()->findOrFail($rental->getKey());
            $payment = $rental->payment()->lockForUpdate()->first();

            abort_unless(
                ($validated['status'] === 'paid' && $rental->status === 'pending')
                    || ($validated['status'] === 'in_use' && $rental->status === 'paid')
                    || ($validated['status'] === 'cancelled' && in_array($rental->status, ['pending', 'paid'], true)),
                409
            );

            $changes = ['status' => $validated['status']];

            if ($validated['status'] === 'paid') {
                abort_if($payment === null || $payment->status !== 'pending', 409, 'Pesanan ini belum memiliki pembayaran yang menunggu konfirmasi.');
                abort_if($payment->expires_at?->isPast(), 409, 'Batas waktu pembayaran pesanan ini telah habis.');

                $setting = Setting::query()->first();
                $distance = $rental->pickup_method === 'delivery'
                    ? (float) $validated['delivery_distance_km']
                    : 0;
                $freeDeliveryKm = (float) ($setting?->free_delivery_km ?? 0);
                $feePerKm = (float) ($setting?->fee_per_km ?? 0);
                $deliveryFee = (int) ceil(max(0, $distance - $freeDeliveryKm) * $feePerKm);

                $changes['delivery_distance_km'] = $rental->pickup_method === 'delivery' ? $distance : null;
                $changes['delivery_fee'] = $deliveryFee;
                $changes['grand_total'] = $rental->subtotal + $deliveryFee;
                $rental->update($changes);

                $payment->update([
                    'amount' => $rental->grand_total + $rental->late_fee_total,
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            } elseif ($validated['status'] === 'cancelled' && $payment?->status === 'pending') {
                $rental->update($changes);
                $payment->update(['status' => 'cancelled']);
            } else {
                $rental->update($changes);
            }
        });

        return back()->with('status', "Status pesanan {$rental->order_number} diperbarui menjadi {$validated['status']}.");
    }

    public function returnRental(Rental $rental): RedirectResponse
    {
        abort_unless($rental->status === 'in_use', 409);

        $lateFee = DB::transaction(function () use ($rental): int {
            $rental = Rental::query()->lockForUpdate()->findOrFail($rental->getKey());
            abort_unless($rental->status === 'in_use', 409);

            $lateDays = today()->greaterThan($rental->end_date)
                ? (int) $rental->end_date->diffInDays(today())
                : 0;
            $quantity = (int) $rental->items()->sum('quantity');
            $feePerDay = (int) (Setting::query()->value('denda_per_hari') ?? 0);
            $lateFee = $lateDays * $quantity * $feePerDay;

            abort_if($lateFee > 0 && $rental->payment === null, 409, 'Pesanan ini belum memiliki data pembayaran untuk mencatat denda.');

            $rental->update([
                'status' => 'returned',
                'returned_at' => now(),
                'late_days' => $lateDays,
                'late_fee_total' => $lateFee,
            ]);

            if ($lateFee > 0 && $rental->payment !== null) {
                $rental->payment->update([
                    'amount' => $rental->payment->amount + $lateFee,
                    'status' => 'pending',
                    'paid_at' => null,
                    'expires_at' => null,
                ]);
            }

            return $lateFee;
        });

        return back()->with('status', $lateFee > 0
            ? 'Pengembalian dicatat. Denda keterlambatan: Rp '.number_format($lateFee, 0, ',', '.').'.'
            : 'Pengembalian dicatat tanpa denda keterlambatan.');
    }

    public function confirmPayment(Rental $rental): RedirectResponse
    {
        DB::transaction(function () use ($rental): void {
            $rental = Rental::query()->lockForUpdate()->findOrFail($rental->getKey());
            abort_unless($rental->status === 'returned' && $rental->payment?->status === 'pending', 409);

            $rental->payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });

        return back()->with('status', 'Pembayaran akhir pesanan berhasil dikonfirmasi.');
    }

    /** @return LengthAwarePaginator<int, Rental> */
    protected function filteredRentals(Request $request): LengthAwarePaginator
    {
        $query = Rental::query()
            ->with(['customer', 'payment', 'items.product'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->paginate(15)->withQueryString();
    }
}
