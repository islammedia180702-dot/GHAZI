<x-layouts.public>
    @php
        $paymentExpired = $rental->payment?->status === 'pending'
            && $rental->payment->expires_at?->isPast();
    @endphp

    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-slate-500 hover:text-emerald-700">&larr; Semua Pesanan</a>

        @if (session('success'))
            <div role="status" class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-black text-slate-900">{{ $rental->order_number }}</h1>
                @php
                    $statusLabels = [
                        'pending' => 'Menunggu Pembayaran',
                        'paid' => 'Lunas',
                        'in_use' => 'Sedang Digunakan',
                        'completed' => 'Selesai',
                        'returned' => 'Dikembalikan',
                        'cancelled' => 'Dibatalkan',
                    ];
                @endphp
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                    {{ $paymentExpired ? 'Pembayaran Kedaluwarsa' : ($statusLabels[$rental->status] ?? $rental->status) }}
                </span>
            </div>

            <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">Tanggal sewa</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $rental->start_date->format('d M Y') }} &rarr; {{ $rental->end_date->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Metode</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $rental->pickup_method === 'delivery' ? 'Diantar ke alamat' : 'Ambil di kantor' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Pelanggan</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $rental->customer->name ?? '-' }}</dd>
                </div>
            </dl>

            @if ($rental->notes)
                <p class="mt-4 text-sm text-slate-600"><span class="font-semibold">Catatan:</span> {{ $rental->notes }}</p>
            @endif
        </div>

        @if ($rental->payment)
            <section class="mt-6 rounded-3xl border border-sky-200 bg-sky-50 p-6">
                <h2 class="text-lg font-bold text-slate-900">Status pembayaran</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-slate-600">Jumlah</dt>
                        <dd class="mt-1 font-black text-slate-900">@rupiah($rental->payment->amount)</dd>
                    </div>
                    <div>
                        <dt class="text-slate-600">Status</dt>
                        <dd class="mt-1 font-bold text-slate-900">
                            {{ $paymentExpired ? 'Kedaluwarsa' : ($rental->payment->status === 'pending' ? 'Menunggu konfirmasi admin/staf' : ucfirst($rental->payment->status)) }}
                        </dd>
                    </div>
                    @if ($rental->payment->expires_at)
                        <div>
                            <dt class="text-slate-600">Batas waktu pembayaran</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $rental->payment->expires_at->format('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <dt class="text-slate-600">Referensi pembayaran</dt>
                        <dd class="mt-1 font-mono font-semibold text-slate-900">{{ $rental->payment->reference }}</dd>
                    </div>
                </dl>
                @if ($paymentExpired)
                    <p role="status" class="mt-4 border-t border-sky-200 pt-4 text-sm leading-relaxed text-red-800">
                        Batas waktu pembayaran telah habis. Pesanan ini tidak dapat dikonfirmasi lagi; batalkan pesanan lalu buat pesanan baru jika masih ingin menyewa.
                    </p>
                @elseif ($rental->payment->status === 'pending')
                    <p class="mt-4 border-t border-sky-200 pt-4 text-sm leading-relaxed text-sky-900">
                        Pembayaran online belum tersedia. Hubungi admin/staf toko untuk instruksi pembayaran; status pesanan akan diperbarui setelah pembayaran dikonfirmasi.
                    </p>
                @endif
            </section>
        @endif

        <div class="mt-6 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-lg font-bold text-slate-900">Detail produk</h2>
            <ul class="mt-4 divide-y divide-slate-100">
                @foreach ($rental->items as $item)
                    <li class="flex items-center justify-between gap-4 py-3">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $item->product->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $item->quantity }} pcs &times; {{ $item->days }} hari &times; @rupiah($item->unit_price)</p>
                        </div>
                        <span class="text-sm font-black text-slate-900">@rupiah($item->subtotal)</span>
                    </li>
                @endforeach
            </ul>

            <dl class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Subtotal</dt>
                    <dd class="font-bold text-slate-900">@rupiah($rental->subtotal)</dd>
                </div>
                @if ($rental->delivery_fee > 0)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Biaya antar</dt>
                        <dd class="font-bold text-slate-900">@rupiah($rental->delivery_fee)</dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-slate-600">Total</dt>
                    <dd class="text-lg font-black text-slate-900">@rupiah($rental->grand_total + $rental->late_fee_total)</dd>
                </div>
            </dl>
        </div>

        @if (in_array($rental->status, ['pending', 'paid'], true))
            <form method="POST" action="{{ route('orders.cancel', $rental->order_number) }}" class="mt-6"
                onsubmit="return confirm('Batalkan pesanan ini?');">
                @csrf
                <button type="submit" class="rounded-full border border-red-300 px-5 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">
                    Batalkan Pesanan
                </button>
            </form>
        @endif
    </div>
</x-layouts.public>