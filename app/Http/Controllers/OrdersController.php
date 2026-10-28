<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Rental;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdersController extends Controller
{
    /**
     * List the rentals belonging to the authenticated customer.
     */
    public function index(Request $request): View
    {
        $rentals = $this->customerRentals($request)->paginate(10);

        return view('orders.index', [
            'rentals' => $rentals,
        ]);
    }

    /**
     * Show the detail of a single rental owned by the customer.
     */
    public function show(Request $request, string $orderNumber): View
    {
        $rental = $this->customerRentals($request)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $rental->load(['items.product', 'payment', 'customer']);

        return view('orders.show', [
            'rental' => $rental,
        ]);
    }

    /**
     * Cancel a rental before it is picked up.
     */
    public function destroy(Request $request, string $orderNumber): RedirectResponse
    {
        $rental = $this->customerRentals($request)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        abort_unless(in_array($rental->status, ['pending', 'paid'], true), 409);

        $rental->update(['status' => 'cancelled']);
        $rental->payment?->update(['status' => 'cancelled']);

        ActivityLog::create([
            'action' => 'rental.cancelled_by_customer',
            'description' => "Order {$rental->order_number} dibatalkan oleh pelanggan.",
            'user_id' => $request->user()->id,
            'subject_type' => Rental::class,
            'subject_id' => $rental->id,
        ]);

        return redirect()->route('orders.index')->with('success', "Order {$rental->order_number} berhasil dibatalkan.");
    }

    /**
     * @return Builder<Rental>
     */
    protected function customerRentals(Request $request): Builder
    {
        return Rental::query()
            ->with(['items', 'payment'])
            ->where('user_id', $request->user()->id)
            ->latest();
    }
}
