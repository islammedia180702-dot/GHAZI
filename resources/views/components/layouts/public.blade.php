<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="GHAZI OUTDOOR - Penyewaan alat outdoor terpercaya: tenda, tas carrier, sleeping bag, kompor portable, dan perlengkapan camping lainnya di Bandung.">
        <meta property="og:title" content="{{ config('app.name') }} - Penyewaan Alat Outdoor">
        <meta property="og:description" content="Sewa alat outdoor lengkap untuk camping, hiking, dan petualangan Anda.">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="GHAZI OUTDOOR">

        <title>{{ config('app.name', 'GHAZI OUTDOOR') }} | Sewa Alat Outdoor</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-stone-50 font-sans text-slate-800 antialiased">
        <div class="flex min-h-screen flex-col">
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
                        <a href="{{ route('catalog') }}" class="transition hover:text-emerald-700">Katalog</a>
                        <a href="{{ route('cart.index') }}" class="transition hover:text-emerald-700">Keranjang</a>
                        @auth
                            <a href="{{ route('orders.index') }}" class="transition hover:text-emerald-700">Pesanan</a>
                        @endauth
                        <a href="{{ route('pages.contact') }}" class="transition hover:text-emerald-700">Kontak</a>
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

            <main class="flex-1">
                @if (session('success'))
                    <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            {{ session('warning') }}
                        </div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-800">
                            {{ session('info') }}
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>

            <footer class="border-t border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 text-sm text-slate-600 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <p>© {{ date('Y') }} GHAZI OUTDOOR. Semua hak dilindungi.</p>
                    <div class="flex items-center gap-5">
                        <a href="{{ route('pages.terms') }}" class="hover:text-emerald-700">Syarat & Ketentuan</a>
                        <a href="{{ route('pages.privacy') }}" class="hover:text-emerald-700">Kebijakan Privasi</a>
                        <a href="{{ route('pages.contact') }}" class="hover:text-emerald-700">Kontak</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>