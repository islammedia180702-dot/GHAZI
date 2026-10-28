<x-layouts.public>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-900">Pesanan Saya</h1>
                <p class="mt-1 text-slate-500">Daftar pesanan penyewaan Anda di GHAZI OUTDOOR.</p>
            </div>
            <a href="{{ route('catalog') }}" class="rounded-full bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">
                &larr; Kembali ke Katalog
            </a>
        </div>

        @if ($rentals->isEmpty())
            <div class="mt-8 rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <p class="text-5xl">🧾</p>
                <h2 class="mt-4 text-xl font-bold text-slate-900">Belum ada pesanan</h2>
                <p class="mt-2 text-slate-500">Mulai dari katalog untuk membuat pesanan pertama Anda.</p>
                <a href="{{ route('catalog') }}" class="mt-5 inline-block rounded-full bg-emerald-700 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-800">
                    Jelajahi Katalog
                </a>
            </div>
        @else
            <div class="mt-8 space-y-4">
                @foreach ($rentals as $rental)
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-lg font-bold text-slate-900">{{ $rental->order_number }}</p>
                                <p class="text-sm text-slate-500">
                                    {{ $rental->start_date->format('d M Y') }} &rarr; {{ $rental->end_date->format('d M Y') }}
                                    &middot; {{ $rental->items->count() }} produk
                                    &middot; {{ $rental->pickup_method === 'delivery' ? 'Diantar' : 'Ambil di kantor' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $paymentExpired = $rental->payment?->status === 'pending'
                                        && $rental->payment->expires_at?->isPast();
                                    $statusLabels = [
                                        'pending' => ['text' => 'Menunggu Pembayaran', 'class' => 'bg-amber-100 text-amber-700'],
                                        'paid' => ['text' => 'Lunas', 'class' => 'bg-sky-100 text-sky-700'],
                                        'in_use' => ['text' => 'Sedang Digunakan', 'class' => 'bg-indigo-100 text-indigo-700'],
                                        'completed' => ['text' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700'],
                                        'returned' => ['text' => 'Dikembalikan', 'class' => 'bg-emerald-100 text-emerald-700'],
                                        'cancelled' => ['text' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-700'],
                                    ];
                                    $badge = $statusLabels[$rental->status] ?? ['text' => $rental->status, 'class' => 'bg-slate-100 text-slate-600'];
                                    if ($paymentExpired) {
                                        $badge = ['text' => 'Pembayaran Kedaluwarsa', 'class' => 'bg-red-100 text-red-700'];
                                    }
                                @endphp
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                                <a href="{{ route('orders.show', $rental->order_number) }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">
                                    Detail &rarr;
                                </a>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="text-sm text-slate-500">Total pesanan</span>
                            <span class="text-base font-black text-slate-900">@rupiah($rental->grand_total + $rental->late_fee_total)</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $rentals->links() }}</div>
        @endif
    </div>
</x-layouts.public>