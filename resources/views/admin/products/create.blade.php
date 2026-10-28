<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-slate-500 hover:text-emerald-700">
                &larr; Kembali ke produk
            </a>
            <h1 class="mt-2 text-xl font-bold text-slate-900">Tambah Produk</h1>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @include('admin.products._form')
        </div>
    </div>
</x-app-layout>
