<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-emerald-700">Administrasi</p>
            <h1 class="text-xl font-bold text-slate-900">Pengaturan Toko</h1>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-bold">Pengaturan belum tersimpan.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <section class="space-y-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Profil toko</h2>
                        <p class="mt-1 text-sm text-slate-500">Informasi yang tampil di halaman kontak dan informasi toko.</p>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="store_name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama toko</label>
                            <input id="store_name" name="store_name" required maxlength="255"
                                value="{{ old('store_name', $setting->store_name) }}"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('store_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="whatsapp" class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor WhatsApp</label>
                            <input id="whatsapp" name="whatsapp" maxlength="32" value="{{ old('whatsapp', $setting->whatsapp) }}"
                                placeholder="62812..."
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('whatsapp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="store_address" class="mb-1.5 block text-sm font-semibold text-slate-700">Alamat toko</label>
                            <textarea id="store_address" name="store_address" rows="2" maxlength="2000"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('store_address', $setting->store_address) }}</textarea>
                            @error('store_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="operational_hours" class="mb-1.5 block text-sm font-semibold text-slate-700">Jam operasional</label>
                            <input id="operational_hours" name="operational_hours" maxlength="255"
                                value="{{ old('operational_hours', $setting->operational_hours) }}"
                                placeholder="08.00–21.00"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('operational_hours')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="latitude" class="mb-1.5 block text-sm font-semibold text-slate-700">Latitude</label>
                                <input id="latitude" name="latitude" type="number" step="any" min="-90" max="90"
                                    value="{{ old('latitude', $setting->latitude) }}"
                                    class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @error('latitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="longitude" class="mb-1.5 block text-sm font-semibold text-slate-700">Longitude</label>
                                <input id="longitude" name="longitude" type="number" step="any" min="-180" max="180"
                                    value="{{ old('longitude', $setting->longitude) }}"
                                    class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @error('longitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="space-y-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Penyewaan dan pengantaran</h2>
                        <p class="mt-1 text-sm text-slate-500">Ongkir final dihitung admin/staf dari jarak yang mereka verifikasi.</p>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="denda_per_hari" class="mb-1.5 block text-sm font-semibold text-slate-700">Denda keterlambatan per unit/hari (Rp)</label>
                            <input id="denda_per_hari" name="denda_per_hari" type="number" min="0" step="1" required
                                value="{{ old('denda_per_hari', $setting->denda_per_hari) }}"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('denda_per_hari')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="pending_expiry_minutes" class="mb-1.5 block text-sm font-semibold text-slate-700">Batas pesanan menunggu (menit)</label>
                            <input id="pending_expiry_minutes" name="pending_expiry_minutes" type="number" min="1" max="10080" step="1" required
                                value="{{ old('pending_expiry_minutes', $setting->pending_expiry_minutes) }}"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('pending_expiry_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="free_delivery_km" class="mb-1.5 block text-sm font-semibold text-slate-700">Jarak gratis sampai (km)</label>
                            <input id="free_delivery_km" name="free_delivery_km" type="number" min="0" max="1000" step="0.01" required
                                value="{{ old('free_delivery_km', $setting->free_delivery_km) }}"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('free_delivery_km')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="fee_per_km" class="mb-1.5 block text-sm font-semibold text-slate-700">Tarif tiap km setelah batas gratis (Rp)</label>
                            <input id="fee_per_km" name="fee_per_km" type="number" min="0" step="1" required
                                value="{{ old('fee_per_km', $setting->fee_per_km) }}"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('fee_per_km')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="max_delivery_km" class="mb-1.5 block text-sm font-semibold text-slate-700">Jarak pengantaran maksimum (km)</label>
                            <input id="max_delivery_km" name="max_delivery_km" type="number" min="0.01" max="1000" step="0.01" required
                                value="{{ old('max_delivery_km', $setting->max_delivery_km) }}"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('max_delivery_km')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="space-y-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Informasi pelanggan</h2>
                        <p class="mt-1 text-sm text-slate-500">Teks ini ditampilkan di halaman syarat dan kebijakan privasi.</p>
                    </div>
                    <div class="grid gap-5">
                        <div>
                            <label for="terms" class="mb-1.5 block text-sm font-semibold text-slate-700">Syarat dan ketentuan</label>
                            <textarea id="terms" name="terms" rows="5" maxlength="20000"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('terms', $setting->terms) }}</textarea>
                            @error('terms')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="privacy_policy" class="mb-1.5 block text-sm font-semibold text-slate-700">Kebijakan privasi</label>
                            <textarea id="privacy_policy" name="privacy_policy" rows="5" maxlength="20000"
                                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('privacy_policy', $setting->privacy_policy) }}</textarea>
                            @error('privacy_policy')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-full bg-emerald-700 px-6 py-3 text-sm font-bold text-white transition hover:bg-emerald-800">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
