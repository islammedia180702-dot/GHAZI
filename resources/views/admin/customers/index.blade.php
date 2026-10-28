<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-700">Akun pelanggan</p>
                <h1 class="text-xl font-bold text-slate-900">Pelanggan Terverifikasi</h1>
            </div>
            <a href="{{ route('admin.customers.create') }}" class="rounded-full bg-emerald-700 px-5 py-2.5 text-center text-sm font-bold text-white hover:bg-emerald-800">
                Tambah Pelanggan
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-6xl space-y-5 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @error('customer')
                <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">{{ $message }}</div>
            @enderror

            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:flex-row">
                <label for="search" class="sr-only">Cari pelanggan</label>
                <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau nomor telepon"
                    class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <button type="submit" class="rounded-full bg-slate-800 px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-900">Cari</button>
            </form>

            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                @if ($customers->isEmpty())
                    <div class="p-10 text-center text-sm text-slate-500">Belum ada pelanggan terverifikasi yang cocok.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[700px] divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Nama</th>
                                    <th class="px-5 py-3 font-semibold">Email</th>
                                    <th class="px-5 py-3 font-semibold">Telepon</th>
                                    <th class="px-5 py-3 font-semibold">Kota</th>
                                    <th class="px-5 py-3 font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($customers as $customer)
                                    <tr>
                                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $customer->name }}</td>
                                        <td class="px-5 py-4 text-slate-600">{{ $customer->email }}</td>
                                        <td class="px-5 py-4 text-slate-600">{{ $customer->phone ?: '—' }}</td>
                                        <td class="px-5 py-4 text-slate-600">{{ $customer->city ?: '—' }}</td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-4">
                                                <a href="{{ route('admin.customers.show', $customer) }}" class="font-bold text-emerald-700 hover:text-emerald-900">Lihat</a>
                                                <a href="{{ route('admin.customers.edit', $customer) }}" class="font-bold text-slate-700 hover:text-slate-900">Ubah</a>
                                                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}"
                                                    onsubmit="return confirm('Hapus akun pelanggan ini? Akun dengan riwayat penyewaan tidak dapat dihapus.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-bold text-red-600 hover:text-red-800">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-100 p-5">{{ $customers->links() }}</div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
