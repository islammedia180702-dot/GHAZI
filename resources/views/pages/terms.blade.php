<x-layouts.public>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-slate-900">Syarat &amp; Ketentuan</h1>
        <div class="mt-6 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <p class="whitespace-pre-line text-sm leading-relaxed text-slate-600">
                {{ $setting->terms ?? 'Syarat dan ketentuan penyewaan alat outdoor.' }}
            </p>
            <ul class="mt-5 space-y-2 text-sm text-slate-600">
                <li>• Pelanggan wajib verifikasi identitas (KTP + selfie) sebelum checkout.</li>
                <li>• Biaya kerusakan berlaku sesuai keterangan tiap produk.</li>
                <li>• Keterlambatan pengembalian dikenakan denda sesuai pengaturan toko.</li>
            </ul>
        </div>
    </div>
</x-layouts.public>