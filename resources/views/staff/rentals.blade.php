<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Kelola Pesanan (Staff)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <form class="flex flex-wrap items-center gap-3" method="GET">
                <select name="status" class="rounded-full border border-slate-300 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                    <option value="">Semua status</option>
                    @foreach (['pending', 'paid', 'in_use', 'completed', 'cancelled', 'returned'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-full bg-emerald-600 px-5 py-2 text-sm font-bold text-white hover:bg-emerald-700">Terapkan</button>
                <a href="{{ route('staff.dashboard') }}" class="ml-auto text-sm font-bold text-slate-500 hover:text-emerald-700">&larr; Dashboard</a>
            </form>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                @if ($rentals->isEmpty())
                    <p class="text-slate-500">Tidak ada pesanan yang ditemukan.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                                    <th class="py-3 pr-4">No. Pesanan</th>
                                    <th class="py-3 pr-4">Pelanggan</th>
                                    <th class="py-3 pr-4">Ambil / Kembali</th>
                                    <th class="py-3 pr-4">Produk</th>
                                    <th class="py-3 pr-4">Total</th>
                                    <th class="py-3 pr-4">Status</th>
                                    <th class="py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rentals as $rental)
                                    <tr class="border-b border-slate-100">
                                        <td class="py-3 pr-4 font-bold text-slate-900">{{ $rental->order_number }}</td>
                                        <td class="py-3 pr-4">{{ $rental->customer->name ?? '-' }}<br><span class="text-xs text-slate-400">{{ $rental->customer->phone ?? '' }}</span></td>
                                        <td class="py-3 pr-4">{{ $rental->start_date->format('d M') }} &rarr; {{ $rental->end_date->format('d M Y') }}</td>
                                        <td class="py-3 pr-4">{{ $rental->items->count() }} pcs</td>
                                        <td class="py-3 pr-4 font-bold">@rupiah($rental->grand_total + $rental->late_fee_total)</td>
                                        <td class="py-3 pr-4">
                                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $rental->hasExpiredPayment() ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $rental->hasExpiredPayment() ? 'Pembayaran Kedaluwarsa' : $rental->status }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <a href="{{ route('rentals.show', $rental) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $rentals->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>