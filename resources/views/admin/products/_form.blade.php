<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
    enctype="multipart/form-data" class="space-y-6 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-8">
    @csrf
    @if ($product->exists)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-bold">Mohon periksa kembali data produk.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama produk</label>
            <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required maxlength="255"
                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="slug" class="mb-1.5 block text-sm font-semibold text-slate-700">Slug URL</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $product->slug) }}" required maxlength="255"
                pattern="[A-Za-z0-9_-]+" class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('slug')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="category_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Kategori</label>
            <select id="category_id" name="category_id" required
                class="w-full rounded-xl border-slate-300 bg-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Pilih kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id) === $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="price_per_day" class="mb-1.5 block text-sm font-semibold text-slate-700">Harga sewa per hari (Rp)</label>
            <input id="price_per_day" name="price_per_day" type="number" min="0" step="1"
                value="{{ old('price_per_day', $product->price_per_day) }}" required
                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('price_per_day')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="damage_fee" class="mb-1.5 block text-sm font-semibold text-slate-700">Biaya kerusakan (Rp)</label>
            <input id="damage_fee" name="damage_fee" type="number" min="0" step="1"
                value="{{ old('damage_fee', $product->damage_fee ?? 0) }}" required
                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('damage_fee')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="stock_total" class="mb-1.5 block text-sm font-semibold text-slate-700">Jumlah stok (unit)</label>
            <input id="stock_total" name="stock_total" type="number" min="0" max="10000" step="1"
                value="{{ old('stock_total', $product->stock_total ?? 0) }}" required
                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('stock_total')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label for="description" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi</label>
            <textarea id="description" name="description" rows="4" maxlength="5000"
                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $product->description) }}</textarea>
            @error('description')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <label for="image" class="mb-1.5 block text-sm font-semibold text-slate-700">Foto utama</label>
            @if ($product->exists && $product->images->isNotEmpty())
                <img src="{{ $product->featured_image_url }}" alt="Foto {{ $product->name }}"
                    class="mb-3 h-32 w-44 rounded-xl bg-slate-100 object-cover">
            @endif
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
            <p class="mt-1.5 text-xs text-slate-500">JPG, PNG, atau WEBP; maksimal 3 MB. Kosongkan jika tidak ingin mengganti foto.</p>
            @error('image')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
            <input type="hidden" name="is_active" value="0">
            <label for="is_active" class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4">
                <input id="is_active" name="is_active" type="checkbox" value="1"
                    @checked((bool) old('is_active', $product->is_active))
                    class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Tampilkan di katalog</span>
                    <span class="mt-1 block text-xs text-slate-500">Nonaktifkan produk tanpa menghapus riwayat pesanan.</span>
                </span>
            </label>
            @error('is_active')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.products.index') }}"
            class="inline-flex justify-center rounded-full border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
            Batal
        </a>
        <button type="submit"
            class="inline-flex justify-center rounded-full bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
            {{ $product->exists ? 'Simpan Perubahan' : 'Simpan Produk' }}
        </button>
    </div>
</form>
