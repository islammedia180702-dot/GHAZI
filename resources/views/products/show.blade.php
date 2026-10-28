<x-layouts.public>
    @php
        $galleryImages = $product->images->map(fn ($img) => [
            'src' => asset('storage/'.$img->path),
            'alt' => $img->alt_text ?? $product->name,
        ]);
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="mb-6 text-sm text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-emerald-700">Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ route('catalog') }}" class="hover:text-emerald-700">Katalog</a>
            <span class="mx-2">/</span>
            <span class="font-semibold text-slate-700">{{ $product->name }}</span>
        </nav>

        <div class="grid gap-8 lg:grid-cols-2">
            <div x-data="{ active: 0, images: @js($galleryImages) }">
                @if ($galleryImages->isNotEmpty())
                    <div class="aspect-[4/3] overflow-hidden rounded-3xl bg-slate-100">
                        <img
                            :src="images[active]?.src"
                            :alt="images[active]?.alt"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='https://placehold.co/900x675?text=GH+AZI+OUTDOOR';"
                            class="h-full w-full object-cover"
                        >
                    </div>

                    @if ($galleryImages->count() > 1)
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach ($galleryImages as $index => $image)
                                <button type="button"
                                    @click="active = {{ $index }}"
                                    :class="active === {{ $index }} ? 'ring-2 ring-emerald-500 ring-offset-2' : ''"
                                    class="h-20 w-24 overflow-hidden rounded-xl bg-slate-100"
                                    aria-label="Lihat foto {{ $index + 1 }}">
                                    <img src="{{ $image['src'] }}" alt="{{ $image['alt'] }}"
                                        loading="lazy" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="aspect-[4/3] overflow-hidden rounded-3xl bg-slate-100">
                        <img
                            src="{{ $product->featured_image_url }}"
                            alt="{{ $product->name }} - {{ $product->category->name ?? 'Alat outdoor' }}"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='https://placehold.co/900x675?text=GH+AZI+OUTDOOR';"
                            class="h-full w-full object-cover"
                        >
                    </div>
                @endif
            </div>
            <div>
                <div class="mb-3 flex flex-wrap items-center gap-3">
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ $product->category->name ?? 'Outdoor' }}
                    </span>
                    @if ($product->stock_total > 0)
                        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                            Stok: {{ $product->stock_total }}
                        </span>
                    @else
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">Stok habis</span>
                    @endif
                </div>

                <h1 class="text-3xl font-black text-slate-900">{{ $product->name }}</h1>

                <div class="mt-2 flex items-center gap-2 text-sm text-slate-500">
                    <span class="text-orange-400">★</span>
                    <span class="font-semibold text-slate-700">{{ number_format((float) $product->rating_average, 1) }}</span>
                </div>

                <p class="mt-4 text-slate-600">{{ $product->description }}</p>

                <div class="mt-6 space-y-3">
                    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                        <div>
                            <p class="text-xs text-slate-400">Harga sewa</p>
                            <p class="text-2xl font-black text-slate-900">@rupiah($product->price_per_day)<span class="text-sm font-medium text-slate-400">/hari</span></p>
                        </div>
                    </div>

                    @if ($product->damage_fee > 0)
                        <p class="text-xs text-slate-500">Biaya kerusakan (jika berlaku): @rupiah($product->damage_fee)</p>
                    @endif
                </div>

                @if ($product->is_active && $product->stock_total > 0)
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-6 flex items-center gap-3">
                        @csrf
                        <div class="flex items-center rounded-full border border-slate-300">
                            <label for="quantity" class="sr-only">Jumlah</label>
                            <input id="quantity" type="number" name="quantity" min="1" max="{{ $product->stock_total }}" value="1"
                                class="w-20 rounded-full px-4 py-3 text-center text-sm focus:outline-none">
                            <span class="px-2 text-sm text-slate-400">/{{ $product->stock_total }}</span>
                        </div>
                        <button type="submit"
                            class="flex-1 rounded-full bg-orange-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-orange-500/30 transition hover:bg-orange-600">
                            Tambah ke Keranjang
                        </button>
                    </form>
                @else
                    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        Produk ini sedang tidak tersedia untuk disewa. Silakan hubungi kami untuk informasi lebih lanjut.
                    </div>
                @endif
            </div>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <section class="mt-14">
                <h2 class="text-xl font-black text-slate-900">Produk terkait</h2>
                <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.public>
