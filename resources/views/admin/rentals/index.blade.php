<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Kelola Pesanan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->has('rental_ids'))
                <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    {{ $errors->first('rental_ids') }}
                </div>
            @endif

            <form class="flex flex-wrap items-center gap-3" method="GET">
                <select name="status" class="rounded-full border border-slate-300 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                    <option value="">Semua status</option>
                    @foreach (['pending', 'paid', 'in_use', 'completed', 'cancelled', 'returned'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. pesanan / pelanggan"
                    class="rounded-full border border-slate-300 px-4 py-2 text-sm focus:border-emerald-500 focus:outline-none">
                <button type="submit" class="rounded-full bg-emerald-600 px-5 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                    Terapkan
                </button>
            </form>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                @if ($rentals->isEmpty())
                    <p class="text-slate-500">Tidak ada pesanan yang ditemukan.</p>
                @else
                    <div class="overflow-x-auto">
                        @if (auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('rentals.bulk-destroy', request()->query()) }}"
                                onsubmit="return confirm('Hapus pesanan terpilih secara permanen beserta detail dan pembayaran terkait?');">
                                @csrf
                                @method('DELETE')
                        @endif
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                                            @if (auth()->user()->isAdmin())
                                                <th class="py-3 pr-4">
                                                    <input type="checkbox" aria-label="Pilih semua pesanan yang dapat dihapus di halaman ini"
                                                        onchange="Array.from(document.getElementsByName('rental_ids[]')).filter((checkbox) => !checkbox.disabled).forEach((checkbox) => checkbox.checked = this.checked)"
                                                        class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                </th>
                                            @endif
                                            <th class="py-3 pr-4">No. Pesanan</th>
                                            <th class="py-3 pr-4">Pelanggan</th>
                                            <th class="py-3 pr-4">Tanggal</th>
                                            <th class="py-3 pr-4">Produk</th>
                                            <th class="py-3 pr-4">Total</th>
                                            <th class="py-3 pr-4">Status</th>
                                            <th class="py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rentals as $rental)
                                            <tr class="border-b border-slate-100">
                                                @if (auth()->user()->isAdmin())
                                                    <td class="py-3 pr-4">
                                                        <input type="checkbox" name="rental_ids[]" value="{{ $rental->id }}"
                                                            aria-label="Pilih pesanan {{ $rental->order_number }}"
                                                            @disabled(! $rental->isBulkDeletable())
                                                            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                    </td>
                                                @endif
                                                <td class="py-3 pr-4 font-bold text-slate-900">{{ $rental->order_number }}</td>
                                                <td class="py-3 pr-4">{{ $rental->customer->name ?? '-' }}<br><span class="text-xs text-slate-400">{{ $rental->customer->phone ?? '' }}</span></td>
                                                <td class="py-3 pr-4">{{ $rental->start_date->format('d M Y') }} &rarr; {{ $rental->end_date->format('d M Y') }}</td>
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
                            @if (auth()->user()->isAdmin())
                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-xs text-slate-500">Pesanan selesai, dikembalikan, dibatalkan, atau dengan pembayaran kedaluwarsa bisa dihapus. Pilih semua hanya mencakup halaman ini.</p>
                                    <button type="submit"
                                        class="rounded-full bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                                        Hapus Pesanan Terpilih
                                    </button>
                                </div>
                            </form>
                            @endif
                    </div>
                    <div class="mt-4">{{ $rentals->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>