<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Dashboard Staff
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-4">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Sedang digunakan (hari ini)</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $todayPickups }}</p>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Jadwal kembali hari ini</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $todayReturnExpected }}</p>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Pengembalian terlambat</p>
                    <p class="mt-3 text-3xl font-black {{ $lateReturns > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $lateReturns }}</p>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Pesanan menunggu</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $pendingRentals }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('staff.rentals') }}" class="rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                    Kelola Pesanan
                </a>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-bold text-slate-900">Jadwal 7 hari ke depan</h3>

                @if ($upcomingRentals->isEmpty())
                    <p class="mt-4 text-slate-500">Tidak ada jadwal dalam 7 hari ke depan.</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                                    <th class="py-3 pr-4">No. Pesanan</th>
                                    <th class="py-3 pr-4">Pelanggan</th>
                                    <th class="py-3 pr-4">Ambil / Kembali</th>
                                    <th class="py-3 pr-4">Produk</th>
                                    <th class="py-3 pr-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcomingRentals as $rental)
                                    <tr class="border-b border-slate-100">
                                        <td class="py-3 pr-4 font-bold text-slate-900">{{ $rental->order_number }}</td>
                                        <td class="py-3 pr-4">{{ $rental->customer->name ?? '-' }}</td>
                                        <td class="py-3 pr-4">{{ $rental->start_date->format('d M') }} &rarr; {{ $rental->end_date->format('d M Y') }}</td>
                                        <td class="py-3 pr-4">{{ $rental->items->count() }} pcs</td>
                                        <td class="py-3 pr-4"><span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">{{ $rental->status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
