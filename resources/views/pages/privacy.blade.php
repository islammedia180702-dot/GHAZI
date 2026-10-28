<x-layouts.public>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-slate-900">Kebijakan Privasi</h1>
        <div class="mt-6 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <p class="whitespace-pre-line text-sm leading-relaxed text-slate-600">
                {{ $setting->privacy_policy ?? 'Data pelanggan digunakan untuk kebutuhan penyewaan dan verifikasi identitas.' }}
            </p>
            <ul class="mt-5 space-y-2 text-sm text-slate-600">
                <li>• NIK disimpan terenkripsi dan hanya ditampilkan ke tim yang berwenang.</li>
                <li>• Foto KTP dan selfie disimpan di penyimpanan privat dan tidak ditampilkan publik.</li>
                <li>• Alamat digunakan hanya untuk keperluan pengiriman.</li>
            </ul>
        </div>
    </div>
</x-layouts.public>