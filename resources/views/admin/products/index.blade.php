<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-700">Inventaris</p>
                <h1 class="text-xl font-bold text-slate-900">Kelola Produk</h1>
            </div>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center justify-center rounded-full bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
                Tambah Produk
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" action="{{ route('admin.products.index') }}" class="grid gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:grid-cols-[1fr_200px_auto]">
                <label class="sr-only" for="search">Cari produk</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama atau slug produk"
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <label class="sr-only" for="status">Status produk</label>
                <select id="status" name="status" class="rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
                <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-slate-700">
                    Terapkan Filter
                </button>
            </form>

            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div>
                        <h2 class="font-bold text-slate-900">Daftar inventaris</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $products->total() }} produk</p>
                    </div>
                </div>

                @if ($products->isEmpty())
                    <div class="px-6 py-14 text-center">
                        <p class="text-lg font-bold text-slate-900">Belum ada produk yang cocok</p>
                        <p class="mt-2 text-sm text-slate-500">Coba ubah filter atau tambahkan produk baru.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th scope="col" class="px-5 py-3 font-semibold">Produk</th>
                                    <th scope="col" class="px-5 py-3 font-semibold">Kategori</th>
                                    <th scope="col" class="px-5 py-3 font-semibold">Harga / hari</th>
                                    <th scope="col" class="px-5 py-3 font-semibold">Stok</th>
                                    <th scope="col" class="px-5 py-3 font-semibold">Status</th>
                                    <th scope="col" class="px-5 py-3 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $product->featured_image_url }}" alt=""
                                                    class="h-12 w-14 rounded-lg bg-slate-100 object-cover">
                                                <div>
                                                    <p class="font-bold text-slate-900">{{ $product->name }}</p>
                                                    <p class="text-xs text-slate-500">{{ $product->slug }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-slate-600">{{ $product->category->name }}</td>
                                        <td class="px-5 py-4 font-semibold text-slate-800">@rupiah($product->price_per_day)</td>
                                        <td class="px-5 py-4">
                                            <span class="font-bold text-slate-900">{{ $product->stock_total }}</span>
                                            <span class="text-slate-500">unit</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span @class([
                                                'rounded-full px-2.5 py-1 text-xs font-bold',
                                                'bg-emerald-50 text-emerald-700' => $product->is_active,
                                                'bg-slate-100 text-slate-600' => ! $product->is_active,
                                            ])>
                                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                                class="font-bold text-emerald-700 hover:text-emerald-900">
                                                Ubah
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                        {{ $products->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
