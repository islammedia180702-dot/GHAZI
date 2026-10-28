<x-app-layout>
    @php
        $paymentExpired = $rental->hasExpiredPayment();
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detail Pesanan {{ $rental->order_number }}
            </h2>
            <a href="{{ route('rentals.index') }}" class="text-sm font-bold text-slate-500 hover:text-emerald-700">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">Detail penyewaan</h3>
                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-slate-500">Pelanggan</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $rental->customer->name ?? '-' }}</dd>
                            <dd class="text-xs text-slate-400">{{ $rental->customer->email }} · {{ $rental->customer->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Tanggal</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $rental->start_date->format('d M Y') }} &rarr; {{ $rental->end_date->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Metode</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $rental->pickup_method === 'delivery' ? 'Diantar' : 'Ambil di kantor' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Catatan / alamat</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $rental->notes ?? '-' }}</dd>
                        </div>
                        @if ($rental->pickup_method === 'delivery')
                            <div>
                                <dt class="text-slate-500">RT/RW</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $rental->delivery_rt_rw ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Nomor rumah</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $rental->delivery_house_number ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Pin lokasi GPS</dt>
                                @if ($rental->delivery_latitude !== null && $rental->delivery_longitude !== null)
                                    <dd class="mt-1 font-semibold text-slate-900">
                                        {{ $rental->delivery_latitude }}, {{ $rental->delivery_longitude }}
                                    </dd>
                                    <dd class="mt-1">
                                        <a href="https://www.google.com/maps?q={{ $rental->delivery_latitude }},{{ $rental->delivery_longitude }}"
                                            target="_blank" rel="noopener noreferrer"
                                            class="font-semibold text-emerald-700 hover:text-emerald-800">
                                            Buka lokasi di peta
                                        </a>
                                    </dd>
                                @else
                                    <dd class="mt-1 font-semibold text-slate-900">Belum dibagikan</dd>
                                @endif
                            </div>
                        @endif
                    </dl>

                    <h4 class="mt-6 text-base font-bold text-slate-900">Produk</h4>
                    <ul class="mt-3 divide-y divide-slate-100">
                        @foreach ($rental->items as $item)
                            <li class="flex items-center justify-between py-3 text-sm">
                                <span class="font-semibold text-slate-800">{{ $item->product->name ?? '-' }}</span>
                                <span class="text-slate-500">{{ $item->quantity }} pcs &times; {{ $item->days }} hari &middot; <strong class="text-slate-900">@rupiah($item->subtotal)</strong></span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($rental->payment)
                        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm">
                            <p class="font-bold text-slate-900">Pembayaran ({{ $rental->payment->method }})</p>
                            <p class="mt-1 text-slate-600">
                                Referensi: <span class="font-mono">{{ $rental->payment->reference }}</span>
                                &middot; Status: <strong>{{ $rental->payment->status }}</strong>
                                @if ($rental->payment->expires_at)
                                    &middot; Kadaluarsa: {{ $rental->payment->expires_at->format('d M H:i') }}
                                @endif
                            </p>
                            <p class="mt-1 text-slate-600">Jumlah: <strong>@rupiah($rental->payment->amount)</strong></p>
                        </div>
                    @endif
                </div>

                <div class="h-fit rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">Proses pesanan</h3>
                    <p class="mt-1 text-sm text-slate-500">Status saat ini: <strong>{{ $paymentExpired ? 'Pembayaran Kedaluwarsa' : $rental->status }}</strong></p>

                    @if (in_array($rental->status, ['pending', 'paid'], true))
                        <form method="POST" action="{{ route('rentals.update-status', $rental) }}" class="mt-4 space-y-3">
                            @csrf
                            @method('PATCH')
                            <label for="status" class="block text-sm font-semibold text-slate-700">Status berikutnya</label>
                            <select id="status" name="status" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                                @if ($rental->status === 'pending')
                                    @unless ($paymentExpired)
                                        <option value="paid">Konfirmasi pembayaran</option>
                                    @endunless
                                    <option value="cancelled">Batalkan pesanan</option>
                                @else
                                    <option value="in_use">Barang diserahkan</option>
                                    <option value="cancelled">Batalkan pesanan</option>
                                @endif
                            </select>
                            @if ($rental->status === 'pending' && $rental->pickup_method === 'delivery')
                                <div>
                                    <label for="delivery_distance_km" class="mb-1.5 block text-sm font-semibold text-slate-700">Jarak pengantaran terverifikasi (km)</label>
                                    <input id="delivery_distance_km" name="delivery_distance_km" type="number" step="0.01" min="0"
                                        max="{{ $setting?->max_delivery_km ?? 20 }}"
                                        value="{{ old('delivery_distance_km', $rental->delivery_distance_km) }}"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                                    <p class="mt-1 text-xs text-slate-500">Gratis sampai {{ $setting?->free_delivery_km ?? 0 }} km, lalu @rupiah($setting?->fee_per_km ?? 0) per km.</p>
                                </div>
                            @endif
                            <button type="submit" class="w-full rounded-full bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                                Simpan Proses
                            </button>
                        </form>
                    @endif

                    @if ($paymentExpired)
                        <p role="status" class="mt-3 rounded-xl bg-red-50 p-3 text-sm text-red-700">
                            Batas pembayaran habis. Batalkan pesanan ini; konfirmasi pembayaran sudah tidak tersedia.
                        </p>
                    @endif

                    @if ($rental->status === 'in_use')
                        <form method="POST" action="{{ route('rentals.return', $rental) }}" class="mt-4">
                            @csrf
                            <p class="mb-3 text-sm text-slate-600">Catat pengembalian untuk menutup penyewaan dan menghitung denda jika terlambat.</p>
                            <button type="submit" class="w-full rounded-full bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                                Catat Pengembalian
                            </button>
                        </form>
                    @endif

                    @if ($rental->status === 'returned' && $rental->payment?->status === 'pending')
                        <form method="POST" action="{{ route('rentals.payment.confirm', $rental) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full rounded-full bg-sky-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-sky-800">
                                Konfirmasi Pembayaran Denda
                            </button>
                        </form>
                    @endif

                    <dl class="mt-6 space-y-2 border-t border-slate-100 pt-4 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Subtotal</dt>
                            <dd class="font-bold text-slate-900">@rupiah($rental->subtotal)</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Antar</dt>
                            <dd class="font-bold text-slate-900">@rupiah($rental->delivery_fee)</dd>
                        </div>
                        @if ($rental->delivery_distance_km !== null)
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Jarak terverifikasi</dt>
                                <dd class="font-bold text-slate-900">{{ number_format((float) $rental->delivery_distance_km, 2, ',', '.') }} km</dd>
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-slate-100 pt-2">
                            <dt class="text-slate-600">Sewa + antar</dt>
                            <dd class="font-bold text-slate-900">@rupiah($rental->grand_total)</dd>
                        </div>
                        @if ($rental->late_fee_total > 0)
                            <div class="flex justify-between">
                                <dt class="text-red-600">Denda ({{ $rental->late_days }} hari)</dt>
                                <dd class="font-bold text-red-700">@rupiah($rental->late_fee_total)</dd>
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-slate-100 pt-2">
                            <dt class="font-semibold text-slate-700">Total keseluruhan</dt>
                            <dd class="text-base font-black text-slate-900">@rupiah($rental->grand_total + $rental->late_fee_total)</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
