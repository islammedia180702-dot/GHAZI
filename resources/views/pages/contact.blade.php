<x-layouts.public>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-slate-900">Hubungi Kami</h1>
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold text-slate-500">Alamat</p>
                <p class="mt-1 text-sm text-slate-800">{{ $setting->store_address ?? '-' }}</p>
                <p class="mt-3 text-sm font-semibold text-slate-500">Jam operasional</p>
                <p class="mt-1 text-sm text-slate-800">{{ $setting->operational_hours ?? '-' }}</p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold text-slate-500">WhatsApp</p>
                <p class="mt-1 text-sm text-slate-800">
                    <a href="https://wa.me/{{ $setting->whatsapp ?? '' }}" target="_blank" rel="noopener" class="font-semibold text-emerald-700 hover:text-emerald-800">
                        {{ $setting->whatsapp ?? '-' }}
                    </a>
                </p>
                <p class="mt-3 text-sm font-semibold text-slate-500">Kontur</p>
                <p class="mt-1 text-sm text-slate-800">
                    <a href="https://maps.google.com/?q={{ $setting->latitude ?? 0 }},{{ $setting->longitude ?? 0 }}" target="_blank" rel="noopener" class="font-semibold text-emerald-700 hover:text-emerald-800">Lihat di Google Maps</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.public>