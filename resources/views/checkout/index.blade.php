<x-layouts.public>
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-slate-900">Checkout</h1>
        <p class="mt-1 text-slate-500">Lengkapi detail penyewaan untuk membuat pesanan.</p>

        <div class="mt-6 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
            Pembayaran online belum tersedia. Setelah pesanan dibuat, admin atau staf akan memperbarui status pembayaran Anda.
        </div>

        @if ($user->verification_status !== 'verified')
            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <p class="font-semibold">Verifikasi identitas masih diperlukan.</p>
                <p class="mt-1">
                    Sebelum checkout, Anda harus memverifikasi identitas.
                    <a href="{{ route('verification.index') }}" class="font-bold underline">Selesaikan verifikasi</a> untuk melanjutkan.
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 space-y-8">
            @csrf

            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-lg font-bold text-slate-900">Daftar produk</h2>
                <ul class="mt-4 divide-y divide-slate-100">
                    @foreach ($items as $item)
                        <li class="flex items-center justify-between gap-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item['product']->featured_image_url }}"
                                    alt="{{ $item['product']->name }}"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='https://placehold.co/120x90?text=GH+AZI+OUTDOOR';"
                                    class="h-14 w-16 rounded-lg object-cover">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $item['product']->name }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ $item['quantity'] }}&times; @rupiah($item['product']->price_per_day) /hari
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                    <span class="text-sm text-slate-500">Subtotal per hari</span>
                    <span class="text-lg font-black text-slate-900">@rupiah($subtotal)</span>
                </div>
            </section>

            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-lg font-bold text-slate-900">Detail penyewaan</h2>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="start_date" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal ambil</label>
                        <input type="date" name="start_date" id="start_date"
                            value="{{ old('start_date', now()->toDateString()) }}"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @error('start_date')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal kembali</label>
                        <input type="date" name="end_date" id="end_date"
                            value="{{ old('end_date', now()->addDay()->toDateString()) }}"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @error('end_date')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <fieldset class="mt-6">
                    <legend class="mb-3 text-sm font-medium text-slate-700">Metode pengambilan</legend>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 transition hover:border-emerald-400">
                            <input type="radio" name="pickup_method" value="pickup"
                                class="mt-1 h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                @checked(old('pickup_method', 'pickup') === 'pickup')>
                            <span>
                                <span class="block font-bold text-slate-900">Ambil di kantor</span>
                                <span class="mt-0.5 block text-xs text-slate-500">Gratis. Datang langsung ke GHAZI OUTDOOR pada jam operasional.</span>
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 transition hover:border-emerald-400">
                            <input type="radio" name="pickup_method" value="delivery"
                                class="mt-1 h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                @checked(old('pickup_method') === 'delivery')>
                            <span>
                                <span class="block font-bold text-slate-900">Diantar (ongkir dihitung admin/staf)</span>
                            <span class="mt-0.5 block text-xs text-slate-500">Siapkan alamat lengkap dan pin GPS. Ongkir final dihitung berdasarkan jarak rute yang diverifikasi admin/staf.</span>
                            </span>
                        </label>
                    </div>
                    @error('pickup_method')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="mt-5">
                    <label for="address" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Nama jalan / kampung dan kelurahan <span class="font-normal text-slate-400">(wajib jika diantar)</span>
                    </label>
                    <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}"
                        placeholder="Contoh: Jl. Cendana, Kelurahan Coblong"
                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    @error('address')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="delivery_rt_rw" class="mb-1.5 block text-sm font-medium text-slate-700">
                            RT/RW <span class="font-normal text-slate-400">(wajib jika diantar)</span>
                        </label>
                        <input type="text" name="delivery_rt_rw" id="delivery_rt_rw"
                            value="{{ old('delivery_rt_rw') }}" placeholder="Contoh: 002/005"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @error('delivery_rt_rw')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="delivery_house_number" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Nomor rumah <span class="font-normal text-slate-400">(wajib jika diantar)</span>
                        </label>
                        <input type="text" name="delivery_house_number" id="delivery_house_number"
                            value="{{ old('delivery_house_number') }}" placeholder="Contoh: 17"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @error('delivery_house_number')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-800">Bagikan lokasi GPS tujuan pengantaran</p>
                    <p class="mt-1 text-xs text-slate-500">Lokasi hanya diambil setelah tombol ditekan. Admin/staf menggunakannya sebagai acuan peta dan tetap memverifikasi jarak rute.</p>
                    <input type="hidden" name="delivery_latitude" id="delivery_latitude" value="{{ old('delivery_latitude') }}">
                    <input type="hidden" name="delivery_longitude" id="delivery_longitude" value="{{ old('delivery_longitude') }}">
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <button type="button" id="share-delivery-location"
                            class="rounded-full bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Ambil Lokasi GPS
                        </button>
                        <p id="delivery-location-status" role="status" aria-live="polite" class="text-sm text-slate-600">
                            @if (old('delivery_latitude') && old('delivery_longitude'))
                                Lokasi GPS tersimpan pada formulir.
                            @else
                                Lokasi GPS belum dibagikan.
                            @endif
                        </p>
                    </div>
                    @error('delivery_latitude')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('delivery_longitude')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <div class="flex items-center justify-between">
                <a href="{{ route('cart.index') }}" class="text-sm font-semibold text-slate-500 hover:text-emerald-700">
                    &larr; Kembali ke Keranjang
                </a>
                <button type="submit"
                    class="rounded-full bg-orange-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-orange-500/30 transition hover:bg-orange-600">
                    Buat Pesanan
                </button>
            </div>
        </form>
    </div>

    <script>
        const shareLocationButton = document.getElementById('share-delivery-location');
        const deliveryLatitude = document.getElementById('delivery_latitude');
        const deliveryLongitude = document.getElementById('delivery_longitude');
        const deliveryLocationStatus = document.getElementById('delivery-location-status');

        shareLocationButton.addEventListener('click', () => {
            if (!navigator.geolocation) {
                deliveryLocationStatus.textContent = 'Browser ini tidak mendukung lokasi GPS.';
                return;
            }

            deliveryLocationStatus.textContent = 'Meminta izin lokasi GPS...';
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    deliveryLatitude.value = position.coords.latitude.toFixed(7);
                    deliveryLongitude.value = position.coords.longitude.toFixed(7);
                    deliveryLocationStatus.textContent = `Lokasi berhasil diambil: ${deliveryLatitude.value}, ${deliveryLongitude.value}`;
                },
                (error) => {
                    deliveryLocationStatus.textContent = error.code === error.PERMISSION_DENIED
                        ? 'Izin lokasi ditolak. Izinkan akses lokasi lalu coba kembali.'
                        : 'Lokasi tidak berhasil diambil. Periksa GPS dan coba kembali.';
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        });
    </script>
</x-layouts.public>
