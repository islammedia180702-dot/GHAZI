<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Verifikasi Identitas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-800">
                Silakan unggah KTP dan selfie Anda agar akun dapat diverifikasi sebelum checkout. Data akan disimpan aman dan hanya diakses oleh admin/staff yang berwenang.
            </div>

            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-white p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Status saat ini</p>
                        <p class="mt-1 text-lg font-bold text-slate-900">
                            {{ ucfirst($user->verification_status ?? 'unverified') }}
                        </p>
                    </div>
                    @if ($user->verification_status === 'rejected' && $user->rejection_reason)
                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                            {{ $user->rejection_reason }}
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label for="nik" class="block text-sm font-medium text-slate-700">NIK</label>
                        <input id="nik" name="nik" type="text" maxlength="16" inputmode="numeric" value="{{ old('nik') }}" class="mt-2 block w-full rounded-xl border border-slate-300 px-3 py-2 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200" required>
                        @error('nik')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="ktp_image" class="block text-sm font-medium text-slate-700">Foto KTP</label>
                            <input id="ktp_image" name="ktp_image" type="file" accept="image/*" class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-700" required>
                            @error('ktp_image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="selfie_image" class="block text-sm font-medium text-slate-700">Foto Selfie dengan KTP</label>
                            <input id="selfie_image" name="selfie_image" type="file" accept="image/*" class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-700" required>
                            @error('selfie_image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="agreement" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" required>
                            <span>Saya menyetujui penggunaan data pribadi ini untuk keperluan verifikasi identitas dan penyewaan alat outdoor. Data disimpan aman dan tidak dipublikasikan.</span>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-full bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">
                            Kirim untuk Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
