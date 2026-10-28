<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-4">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Jadwal hari ini</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $todayRentals }}</p>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Total pesanan lunas</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $paidRentals }}</p>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Pendapatan hari ini</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">@rupiah($todayRevenue)</p>
                </div>
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">Antrean verifikasi</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $pendingVerification }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.products.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    Kelola Produk
                </a>
                <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    Kelola Kategori
                </a>
                <a href="{{ route('admin.settings.edit') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    Pengaturan Toko
                </a>
                <a href="{{ route('rentals.index') }}" class="rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                    Kelola Pesanan
                </a>
                <a href="{{ route('admin.verifications') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    Verifikasi Identitas
                </a>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Pesanan terbaru</h3>
                    <a href="{{ route('rentals.index') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">Lihat semua &rarr;</a>
                </div>

                @if ($recentRentals->isEmpty())
                    <p class="mt-4 text-slate-500">Belum ada pesanan yang masuk.</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                                    <th class="py-3 pr-4">No. Pesanan</th>
                                    <th class="py-3 pr-4">Pelanggan</th>
                                    <th class="py-3 pr-4">Tanggal</th>
                                    <th class="py-3 pr-4">Total</th>
                                    <th class="py-3 pr-4">Status</th>
                                    <th class="py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentRentals as $rental)
                                    <tr class="border-b border-slate-100">
                                        <td class="py-3 pr-4 font-bold text-slate-900">{{ $rental->order_number }}</td>
                                        <td class="py-3 pr-4">{{ $rental->customer->name ?? '-' }}</td>
                                        <td class="py-3 pr-4">{{ $rental->start_date->format('d M Y') }}</td>
                                        <td class="py-3 pr-4 font-bold">@rupiah($rental->grand_total)</td>
                                        <td class="py-3 pr-4"><span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">{{ $rental->status }}</span></td>
                                        <td class="py-3">
                                            <a href="{{ route('rentals.show', $rental) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">Detail</a>
                                        </td>
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
