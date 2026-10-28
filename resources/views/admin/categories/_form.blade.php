<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
    class="space-y-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
    @csrf
    @if ($category->exists)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama kategori</label>
        <input id="name" name="name" required maxlength="255" value="{{ old('name', $category->name) }}"
            class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="slug" class="mb-1.5 block text-sm font-semibold text-slate-700">Slug URL</label>
        <input id="slug" name="slug" required maxlength="255" pattern="[A-Za-z0-9_-]+"
            value="{{ old('slug', $category->slug) }}"
            class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="description" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" maxlength="2000"
            class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $category->description) }}</textarea>
        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.categories.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-center text-sm font-bold text-slate-700 hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-full bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">
            {{ $category->exists ? 'Simpan Perubahan' : 'Simpan Kategori' }}
        </button>
    </div>
</form>
