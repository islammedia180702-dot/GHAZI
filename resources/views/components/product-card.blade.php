@props(['product'])

<article class="group relative flex flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:shadow-lg">
    <a href="{{ route('products.show', $product) }}" class="block aspect-[4/3] overflow-hidden bg-slate-100">
        <img
            src="{{ $product->featured_image_url }}"
            alt="{{ $product->name }} - {{ $product->category->name ?? 'Alat outdoor' }}"
            loading="lazy"
            @if (! Str::startsWith($product->featured_image_url, url('/storage/')))
                onerror="this.onerror=null;this.src='https://placehold.co/900x675?text=GH+AZI+OUTDOOR';"
            @endif
            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
        >
    </a>

    <div class="flex flex-1 flex-col p-4 sm:p-5">
        <div class="mb-2 flex items-center justify-between gap-2">
            <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                {{ $product->category->name ?? 'Outdoor' }}
            </span>

            @if ($product->stock_total > 0)
                <span class="rounded-full bg-orange-50 px-2.5 py-0.5 text-xs font-semibold text-orange-700">
                    @if ($product->stock_total <= 3)
                        Sisa {{ $product->stock_total }}
                    @else
                        Stok tersedia
                    @endif
                </span>
            @else
                <span class="rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-600">Stok habis</span>
            @endif
        </div>

        <h3 class="text-base font-bold text-slate-900">
            <a href="{{ route('products.show', $product) }}" class="hover:text-emerald-700">{{ $product->name }}</a>
        </h3>

        <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ Str::limit($product->description, 60) }}</p>

        <div class="mt-auto flex items-end justify-between gap-3 pt-4">
            <div>
                <p class="text-xs text-slate-400">Mulai dari</p>
                <p class="text-lg font-black text-slate-900">@rupiah($product->price_per_day)<span class="text-xs font-medium text-slate-400">/hari</span></p>
            </div>

            <form action="{{ route('cart.add', $product) }}" method="POST" class="relative">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" @if (! $product->is_active || $product->stock_total <= 0) disabled @endif
                    class="rounded-full bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-40">
                    + Keranjang
                </button>
            </form>
        </div>
    </div>
</article>