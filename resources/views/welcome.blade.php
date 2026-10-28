<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GHAZI OUTDOOR | Sewa Alat Outdoor</title>
        <meta name="description" content="GHAZI OUTDOOR menyediakan penyewaan alat outdoor seperti tenda, tas, perlengkapan masak, dan peralatan trekking.">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-50 text-slate-800 antialiased">
        <header class="sticky top-0 z-50 border-b border-emerald-900/10 bg-white/90 backdrop-blur-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-700 text-lg font-bold text-white shadow-lg shadow-emerald-900/20">G</div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700">GHAZI</p>
                        <p class="text-lg font-black tracking-wide text-slate-900">OUTDOOR</p>
                    </div>
                </div>

                <nav class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex">
                    <a href="#catalog" class="transition hover:text-emerald-700">Katalog</a>
                    <a href="#why-us" class="transition hover:text-emerald-700">Kenapa Kami</a>
                    <a href="#reviews" class="transition hover:text-emerald-700">Ulasan</a>
                    <a href="#contact" class="transition hover:text-emerald-700">Kontak</a>
                </nav>

                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-emerald-700 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-full bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Daftar</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <main>
            <section class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-800 to-emerald-700 text-white">
                <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.2), transparent 20%), radial-gradient(circle at 80% 10%, rgba(234,88,12,0.25), transparent 20%), radial-gradient(circle at 50% 70%, rgba(255,255,255,0.12), transparent 25%);"></div>
                <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
                    <div class="flex flex-col justify-center">
                        <span class="mb-5 inline-flex w-fit items-center rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-50">Outdoor Gear Rental</span>
                        <h1 class="max-w-xl text-4xl font-black leading-tight sm:text-5xl lg:text-6xl">Siap menjelajah alam, tanpa ribet bawa perlengkapan.</h1>
                        <p class="mt-6 max-w-xl text-base text-emerald-50/90 sm:text-lg">
                            GHAZI OUTDOOR menyediakan alat outdoor berkualitas untuk camping, hiking, dan petualangan Anda dengan sistem sewa mudah, cepat, dan aman.
                        </p>

                        <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                            <a href="#catalog" class="inline-flex items-center justify-center rounded-full bg-orange-500 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-orange-500/30 transition hover:bg-orange-400">Sewa Sekarang</a>
                            <a href="#contact" class="inline-flex items-center justify-center rounded-full border border-white/30 bg-white/5 px-6 py-3 text-base font-semibold text-white transition hover:bg-white/10">Hubungi Kami</a>
                        </div>

                        <div class="mt-10 grid max-w-lg grid-cols-3 gap-4 text-left">
                            <div>
                                <p class="text-3xl font-black">500+</p>
                                <p class="text-sm text-emerald-100">Pelanggan puas</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black">120+</p>
                                <p class="text-sm text-emerald-100">Unit tersedia</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black">4.9/5</p>
                                <p class="text-sm text-emerald-100">Rating layanan</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="relative mx-auto max-w-xl overflow-hidden rounded-[28px] border border-white/20 bg-white/10 p-4 shadow-[0_40px_80px_rgba(2,44,27,0.45)] backdrop-blur-sm">
                            <div class="overflow-hidden rounded-[22px] bg-stone-200">
                                <img src="https://images.unsplash.com/photo-1527631746610-bca00a040d60?auto=format&fit=crop&w=1200&q=80" alt="Outdoor equipment" class="h-[520px] w-full object-cover">
                            </div>
                            <div class="absolute -bottom-6 left-8 right-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-xl">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Popular now</p>
                                        <h2 class="mt-1 text-xl font-bold text-slate-900">Paket Camping Premium</h2>
                                    </div>
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Tersedia</span>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-slate-500">Mulai dari</p>
                                        <p class="text-2xl font-black text-slate-900">Rp 145.000</p>
                                    </div>
                                    <button class="rounded-full bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-400">Pesan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="why-us" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="mb-10 text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Kenapa pilih kami</p>
                    <h2 class="mt-4 text-3xl font-black text-slate-900 sm:text-4xl">Perlengkapan outdoor siap pakai, kualitas terjamin</h2>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-xl text-emerald-700">✅</div>
                        <h3 class="text-xl font-bold text-slate-900">Kondisi alat terawat</h3>
                        <p class="mt-3 text-slate-600">Setiap unit diperiksa sebelum diserahkan agar perjalanan Anda aman dan nyaman.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-xl text-orange-600">⚡</div>
                        <h3 class="text-xl font-bold text-slate-900">Proses cepat</h3>
                        <p class="mt-3 text-slate-600">Dari pemesanan hingga pembayaran, kami fokus pada pengalaman yang mudah dan efisien.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-xl text-amber-700">🧭</div>
                        <h3 class="text-xl font-bold text-slate-900">Pengiriman fleksibel</h3>
                        <p class="mt-3 text-slate-600">Ambil di kantor atau antar ke lokasi Anda sesuai kebutuhan untuk perjalanan yang praktis.</p>
                    </div>
                </div>
            </section>

            <section id="catalog" class="bg-slate-100 py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Katalog favorit</p>
                            <h2 class="mt-4 text-3xl font-black text-slate-900">Pilih perlengkapan yang sesuai petualangan Anda</h2>
                        </div>
                        <a href="#" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Lihat semua produk</a>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @forelse($products as $product)
                            <article class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="relative h-64 overflow-hidden bg-slate-200">
                                    <img src="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1200&q=80" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                                    <span class="absolute left-4 top-4 rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white">Tersedia</span>
                                </div>
                                <div class="p-5">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $product->category->name ?? 'Outdoor' }}</p>
                                    <h3 class="mt-2 text-xl font-bold text-slate-900">{{ $product->name }}</h3>
                                    <p class="mt-2 text-sm text-slate-600">{{ Str::limit($product->description ?? 'Alat outdoor siap pakai untuk kebutuhan camping dan trekking.', 90) }}</p>
                                    <div class="mt-5 flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-slate-500">Per hari</p>
                                            <p class="text-2xl font-black text-slate-900">Rp {{ number_format($product->price_per_day, 0, ',', '.') }}</p>
                                        </div>
                                        <button class="rounded-full bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Tambah ke Keranjang</button>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500 md:col-span-2 xl:col-span-3">
                                Produk belum tersedia. Nantikan katalog baru GHAZI OUTDOOR.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="mb-10 text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Cara sewa</p>
                    <h2 class="mt-4 text-3xl font-black text-slate-900">Tiga langkah sederhana</h2>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-lg font-black text-emerald-700">1</div>
                        <h3 class="text-xl font-bold text-slate-900">Pilih produk</h3>
                        <p class="mt-3 text-slate-600">Pilih kebutuhan alat outdoor Anda sesuai durasi dan jumlah barang.</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 text-lg font-black text-orange-600">2</div>
                        <h3 class="text-xl font-bold text-slate-900">Checkout</h3>
                        <p class="mt-3 text-slate-600">Tentukan metode ambil atau antar, lalu bayar dengan QRIS atau transfer.</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-lg font-black text-amber-700">3</div>
                        <h3 class="text-xl font-bold text-slate-900">Nikmati petualangan</h3>
                        <p class="mt-3 text-slate-600">Ambil alat di kantor atau nikmati layanan pengiriman sesuai jadwal Anda.</p>
                    </div>
                </div>
            </section>

            <section id="reviews" class="bg-emerald-950 py-16 text-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-10 text-center">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-200">Ulasan pelanggan</p>
                        <h2 class="mt-4 text-3xl font-black">Dibangun berdasarkan pengalaman nyata pelanggan</h2>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                            <div class="mb-4 text-orange-400">★★★★★</div>
                            <p class="text-slate-100">“Proses penyewaan cepat dan alatnya sangat lengkap. Petualangan kami makin lancar dengan GHAZI OUTDOOR.”</p>
                            <div class="mt-6 font-semibold text-white">Rizky</div>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                            <div class="mb-4 text-orange-400">★★★★★</div>
                            <p class="text-slate-100">“Kualitas perlengkapan sangat bagus dan staffnya ramah. Recommended untuk camping keluarga.”</p>
                            <div class="mt-6 font-semibold text-white">Nadia</div>
                        </div>
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                            <div class="mb-4 text-orange-400">★★★★★</div>
                            <p class="text-slate-100">“Layanan pengantaran membuat hiking jadi lebih mudah. Sudah jadi pilihan utama kami.”</p>
                            <div class="mt-6 font-semibold text-white">Fajar</div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contact" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid gap-8 rounded-[32px] bg-slate-900 p-8 text-white md:grid-cols-2 md:p-10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-300">Kontak kami</p>
                        <h2 class="mt-4 text-3xl font-black">Siap bantu rencanakan petualangan Anda</h2>
                        <p class="mt-4 max-w-lg text-slate-300">Informasi lengkap mengenai alat, jadwal ambil, dan pengiriman tersedia melalui WhatsApp atau datang langsung ke kantor.</p>
                    </div>
                    <div class="grid gap-4 rounded-3xl bg-white/5 p-6 text-sm text-slate-200">
                        <p><span class="font-semibold text-white">Alamat:</span> Jl. Raya Bandung No. 123, Bandung, Jawa Barat</p>
                        <p><span class="font-semibold text-white">WhatsApp:</span> +62 812-3456-7890</p>
                        <p><span class="font-semibold text-white">Jam Operasional:</span> 08.00 - 21.00</p>
                        <a href="https://wa.me/6281234567890" class="mt-3 inline-flex w-fit items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-400">Chat WhatsApp</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 text-sm text-slate-600 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <p>© 2026 GHAZI OUTDOOR. Semua hak dilindungi.</p>
                <div class="flex items-center gap-5">
                    <a href="#" class="hover:text-emerald-700">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-emerald-700">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-emerald-700">Kontak</a>
                </div>
            </div>
        </footer>
    </body>
</html>
