<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Staff overview: today's pickup/dropoff, late returns, and stock check.
     */
    public function index(): View
    {
        $todayPickups = Rental::where('status', 'in_use')->whereDate('start_date', today())->count();
        $todayReturnExpected = Rental::where('status', 'in_use')->whereDate('end_date', today())->count();
        $lateReturns = Rental::where('status', 'in_use')->whereDate('end_date', '<', today())->count();
        $pendingRentals = Rental::where('status', 'pending')->count();

        $upcomingRentals = Rental::with(['items.product', 'customer'])
            ->whereIn('status', ['pending', 'in_use'])
            ->whereDate('start_date', '<=', now()->addDays(7))
            ->orderBy('start_date')
            ->take(10)
            ->get();

        return view('staff.dashboard', [
            'todayPickups' => $todayPickups,
            'todayReturnExpected' => $todayReturnExpected,
            'lateReturns' => $lateReturns,
            'pendingRentals' => $pendingRentals,
            'upcomingRentals' => $upcomingRentals,
        ]);
    }
}
