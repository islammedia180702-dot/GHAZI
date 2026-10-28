<x-layouts.public>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-slate-900">Keranjang</h1>
        <p class="mt-1 text-slate-500">Pilih jumlah dan lanjutkan ke checkout.</p>

        @if (empty($items))
            <div class="mt-8 rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <p class="text-5xl">🎒</p>
                <h2 class="mt-4 text-xl font-bold text-slate-900">Keranjang masih kosong</h2>
                <p class="mt-2 text-slate-500">Belum ada produk yang ditambahkan.</p>
                <a href="{{ route('catalog') }}" class="mt-5 inline-block rounded-full bg-emerald-700 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-800">
                    Jelajahi Katalog
                </a>
            </div>
        @else
            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_360px]">
                <div class="space-y-4">
                    @foreach ($items as $item)
                        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center">
                            <img
                                src="{{ $item['product']->featured_image_url }}"
                                alt="{{ $item['product']->name }}"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='https://placehold.co/300x225?text=GH+AZI+OUTDOOR';"
                                class="h-24 w-32 rounded-xl object-cover">
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-slate-900">
                                    <a href="{{ route('products.show', $item['product']) }}" class="hover:text-emerald-700">
                                        {{ $item['product']->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-slate-500">{{ $item['product']->category->name ?? 'Outdoor' }}</p>
                                <p class="mt-1 text-sm text-slate-700">@rupiah($item['product']->price_per_day) /hari</p>
                            </div>

                            <form action="{{ route('cart.update', $item['product']) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                    max="{{ $item['product']->stock_total }}"
                                    class="w-20 rounded-full border border-slate-300 px-3 py-2 text-center text-sm focus:border-emerald-500 focus:outline-none">
                                <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                    Simpan
                                </button>
                            </form>

                            <form action="{{ route('cart.remove', $item['product']) }}" method="POST"
                                onsubmit="return confirm('Hapus {{ $item['product']->name }} dari keranjang?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <aside class="h-fit rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">Ringkasan</h2>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Subtotal (per hari)</dt>
                            <dd class="font-bold text-slate-900">@rupiah($subtotal)</dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-xs text-slate-400">
                        Total akhir dihitung berdasarkan jumlah hari sewa dan metode pengambilan saat checkout.
                    </p>

                    <a href="{{ route('checkout') }}"
                        class="mt-5 block rounded-full bg-emerald-700 px-5 py-3 text-center text-sm font-bold text-white shadow-lg shadow-emerald-700/25 transition hover:bg-emerald-800">
                        Lanjut ke Checkout
                    </a>
                    <a href="{{ route('catalog') }}"
                        class="mt-3 block rounded-full border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Lanjut Belanja
                    </a>
                </aside>
            </div>
        @endif
    </div>
</x-layouts.public>