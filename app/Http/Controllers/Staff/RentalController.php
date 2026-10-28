<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalController extends Controller
{
    /**
     * Staff rental list with a quick filter by status.
     */
    public function index(Request $request): View
    {
        $rentals = Rental::query()
            ->with(['customer', 'items.product', 'payment'])
            ->latest()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->paginate(20)
            ->withQueryString();

        return view('staff.rentals', [
            'rentals' => $rentals,
        ]);
    }
}
