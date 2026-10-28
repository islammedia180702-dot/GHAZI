<x-layouts.public>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-2">
            <h1 class="text-3xl font-black text-slate-900">Katalog Perlengkapan Outdoor</h1>
            <p class="text-slate-500">Temukan alat yang Anda butuhkan untuk petualangan berikutnya.</p>
        </div>

        <div class="mb-8 grid gap-6 lg:grid-cols-[260px_1fr]">
            <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Filter</h2>

                <form method="GET" action="{{ route('catalog') }}" class="mt-4 space-y-4">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Cari produk</label>
                        <input id="search" type="text" name="search" value="{{ request('search') }}"
                            placeholder="Contoh: tenda, kompor..."
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    </div>

                    <div>
                        <label for="category" class="mb-2 block text-sm font-medium text-slate-700">Kategori</label>
                        <select id="category" name="category"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) request('category') === $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full rounded-full bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                        Terapkan Filter
                    </button>
                </form>
            </aside>

            <div>
                <div class="mb-4 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                    <span>{{ $products->total() }} produk ditemukan</span>
                    @if (request('search'))
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            Pencarian: "{{ request('search') }}"
                        </span>
                    @endif
                </div>

                @if ($products->isEmpty())
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                        <p class="text-4xl">🏕️</p>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">Tidak ada produk ditemukan</h3>
                        <p class="mt-2 text-sm text-slate-500">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                        <a href="{{ route('catalog') }}" class="mt-4 inline-block rounded-full bg-emerald-700 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                            Reset Filter
                        </a>
                    </div>
                @else
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.public>