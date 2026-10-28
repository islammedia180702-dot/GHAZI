<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-slate-700">Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>!</p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <a href="{{ route('orders.index') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:ring-emerald-400">
                    <p class="text-2xl">🧾</p>
                    <p class="mt-3 text-lg font-bold text-slate-900">Pesanan Saya</p>
                    <p class="mt-1 text-sm text-slate-500">Lihat status dan detail pesanan penyewaan Anda.</p>
                </a>

                <a href="{{ route('verification.index') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:ring-emerald-400">
                    <p class="text-2xl">🪪</p>
                    <p class="mt-3 text-lg font-bold text-slate-900">Verifikasi Identitas</p>
                    <p class="mt-1 text-sm text-slate-500">
                        Status Anda:
                        <strong class="ml-1 @if (Auth::user()->verification_status === 'verified') text-emerald-700 @elseif (Auth::user()->verification_status === 'pending') text-amber-600 @else text-slate-900 @endif">
                            {{ ucfirst(Auth::user()->verification_status) }}
                        </strong>
                    </p>
                </a>

                <a href="{{ route('catalog') }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:ring-emerald-400">
                    <p class="text-2xl">🎒</p>
                    <p class="mt-3 text-lg font-bold text-slate-900">Katalog</p>
                    <p class="mt-1 text-sm text-slate-500">Sewa alat outdoor untuk petualangan berikutnya.</p>
                </a>
            </div>

            @if (Auth::user()->isAdmin())
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Dashboard Admin</a>
                    <a href="{{ route('admin.products.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Kelola Produk</a>
                    <a href="{{ route('rentals.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Kelola Pesanan</a>
                    <a href="{{ route('admin.verifications') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Verifikasi</a>
                </div>
            @elseif (Auth::user()->isStaff())
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('staff.dashboard') }}" class="rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Dashboard Staff</a>
                    <a href="{{ route('staff.rentals') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Kelola Pesanan</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
