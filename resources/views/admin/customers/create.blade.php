<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('admin.customers.index') }}" class="text-sm font-semibold text-slate-500 hover:text-emerald-700">&larr; Kembali ke pelanggan</a>
            <h1 class="mt-2 text-xl font-bold text-slate-900">Tambah Pelanggan Terverifikasi</h1>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @include('admin.customers._form')
        </div>
    </div>
</x-app-layout>
