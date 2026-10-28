<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('admin.customers.index') }}" class="text-sm font-semibold text-slate-500 hover:text-emerald-700">&larr; Kembali ke pelanggan</a>
                <h1 class="mt-2 text-xl font-bold text-slate-900">Detail Pelanggan</h1>
            </div>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="rounded-full bg-emerald-700 px-5 py-2.5 text-center text-sm font-bold text-white hover:bg-emerald-800">
                Ubah Akun
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="space-y-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
                <div>
                    <p class="text-sm text-slate-500">Nama lengkap</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $customer->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Email</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $customer->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Nomor telepon</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $customer->phone ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Alamat</p>
                    <p class="mt-1 whitespace-pre-line font-semibold text-slate-900">{{ $customer->address ?: '—' }}</p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <p class="text-sm text-slate-500">Kota</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ $customer->city ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Kecamatan</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ $customer->kecamatan ?: '—' }}</p>
                    </div>
                </div>
                <div class="border-t border-slate-100 pt-5">
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Terverifikasi</span>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
