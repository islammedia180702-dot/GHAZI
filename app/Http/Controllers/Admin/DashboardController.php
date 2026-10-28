<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin overview: today's schedule, revenue, verification queue, and recent orders.
     */
    public function index(): View
    {
        $todayRentals = Rental::whereDate('start_date', today())->count();

        $paidRentals = Rental::where('status', 'paid')->count();
        $todayRevenue = Rental::where('status', 'paid')
            ->whereDate('created_at', today())
            ->sum('grand_total');

        $pendingVerification = User::where('verification_status', 'pending')->count();

        $recentRentals = Rental::with(['items.product', 'customer', 'payment'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', [
            'todayRentals' => $todayRentals,
            'paidRentals' => $paidRentals,
            'todayRevenue' => $todayRevenue,
            'pendingVerification' => $pendingVerification,
            'recentRentals' => $recentRentals,
        ]);
    }
}
