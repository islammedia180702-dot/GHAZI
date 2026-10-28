<form method="POST" action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}"
    class="space-y-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-7">
    @csrf
    @if ($customer->exists)
        @method('PUT')
    @endif

    <p class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
        Akun yang dikelola di halaman ini berstatus pelanggan terverifikasi. Peran dan status verifikasi tidak dapat diubah dari formulir ini.
    </p>

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama lengkap</label>
        <input id="name" name="name" required maxlength="255" value="{{ old('name', $customer->name) }}"
            class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
        <input id="email" name="email" type="email" required maxlength="255" value="{{ old('email', $customer->email) }}"
            class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="phone" class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor telepon</label>
        <input id="phone" name="phone" type="tel" required minlength="8" maxlength="32" value="{{ old('phone', $customer->phone) }}"
            class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="address" class="mb-1.5 block text-sm font-semibold text-slate-700">Alamat</label>
        <textarea id="address" name="address" rows="3" required minlength="10" maxlength="2000"
            class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('address', $customer->address) }}</textarea>
        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="city" class="mb-1.5 block text-sm font-semibold text-slate-700">Kota</label>
            <input id="city" name="city" required maxlength="255" value="{{ old('city', $customer->city) }}"
                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="kecamatan" class="mb-1.5 block text-sm font-semibold text-slate-700">Kecamatan</label>
            <input id="kecamatan" name="kecamatan" required maxlength="255" value="{{ old('kecamatan', $customer->kecamatan) }}"
                class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            @error('kecamatan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
    <div>
        <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">
            Kata sandi {{ $customer->exists ? '(kosongkan jika tidak ingin mengganti)' : '' }}
        </label>
        <input id="password" name="password" type="password" {{ $customer->exists ? '' : 'required' }} minlength="8" autocomplete="new-password"
            class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Konfirmasi kata sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" {{ $customer->exists ? '' : 'required' }} minlength="8" autocomplete="new-password"
            class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.customers.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-center text-sm font-bold text-slate-700 hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-full bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-800">
            {{ $customer->exists ? 'Simpan Perubahan' : 'Simpan Pelanggan' }}
        </button>
    </div>
</form>
