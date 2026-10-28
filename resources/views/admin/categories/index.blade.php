<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-700">Katalog</p>
                <h1 class="text-xl font-bold text-slate-900">Kelola Kategori</h1>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="rounded-full bg-emerald-700 px-5 py-2.5 text-center text-sm font-bold text-white hover:bg-emerald-800">
                Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-5xl space-y-5 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @error('category')
                <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ $message }}</div>
            @enderror

            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                @if ($categories->isEmpty())
                    <div class="p-10 text-center text-sm text-slate-500">Belum ada kategori.</div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($categories as $category)
                            <article class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="font-bold text-slate-900">{{ $category->name }}</h2>
                                    <p class="mt-1 text-xs text-slate-500">{{ $category->slug }} · {{ $category->products_count }} produk</p>
                                    @if ($category->description)
                                        <p class="mt-2 text-sm text-slate-600">{{ $category->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-900">Ubah</a>
                                    @if ($category->products_count === 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                            onsubmit="return confirm('Hapus kategori ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-bold text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">Tidak dapat dihapus</span>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="border-t border-slate-100 p-5">{{ $categories->links() }}</div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
