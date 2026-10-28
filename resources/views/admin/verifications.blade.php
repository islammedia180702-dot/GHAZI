<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Verifikasi Identitas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-bold text-slate-900">Antrean verifikasi</h3>

                @if ($queue->isEmpty())
                    <p class="mt-4 text-slate-500">Tidak ada data yang menunggu verifikasi saat ini.</p>
                @else
                    <div class="mt-6 space-y-4">
                        @foreach ($queue as $user)
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="text-lg font-bold text-slate-900">{{ $user->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $user->email }} · {{ $user->phone ?? 'Belum ada nomor telepon' }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                                        @if ($user->verification_status === 'pending') bg-amber-100 text-amber-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ ucfirst($user->verification_status) }}
                                    </span>
                                </div>

                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    @if ($user->ktp_image)
                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                            <p class="mb-2 text-sm font-medium text-slate-700">Foto KTP</p>
                                            <img src="{{ route('admin.verifications.file', ['user' => $user, 'type' => 'ktp']) }}" alt="KTP {{ $user->name }}" class="h-44 w-full rounded-lg object-cover">
                                        </div>
                                    @endif

                                    @if ($user->selfie_image)
                                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                            <p class="mb-2 text-sm font-medium text-slate-700">Foto Selfie</p>
                                            <img src="{{ route('admin.verifications.file', ['user' => $user, 'type' => 'selfie']) }}" alt="Selfie {{ $user->name }}" class="h-44 w-full rounded-lg object-cover">
                                        </div>
                                    @endif
                                </div>

                                @if ($user->verification_status === 'rejected' && $user->rejection_reason)
                                    <p class="mt-3 text-sm text-red-600">Alasan penolakan: {{ $user->rejection_reason }}</p>
                                @endif

                                <div class="mt-5 flex flex-wrap items-center gap-3">
                                    <form method="POST" action="{{ route('admin.verifications.approve', $user) }}">
                                        @csrf
                                        <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                            Setujui
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.verifications.reject', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="text" name="reason" placeholder="Alasan penolakan" class="rounded-full border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                                        <button type="submit" class="rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-bold text-slate-900">Pelanggan terverifikasi</h3>
                <p class="mt-1 text-sm text-slate-500">Status verifikasi dicatat pada akun pelanggan yang bersangkutan.</p>

                @if ($verifiedCustomers->isEmpty())
                    <p class="mt-4 text-slate-500">Belum ada pelanggan terverifikasi.</p>
                @else
                    <div class="mt-5 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                                    <th class="py-3 pr-4">Pelanggan</th>
                                    <th class="py-3 pr-4">Telepon</th>
                                    <th class="py-3 pr-4">Status</th>
                                    <th class="py-3 pr-4">Diverifikasi oleh</th>
                                    <th class="py-3">Tanggal verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($verifiedCustomers as $customer)
                                    <tr class="border-b border-slate-100">
                                        <td class="py-3 pr-4">
                                            <span class="font-semibold text-slate-900">{{ $customer->name }}</span>
                                            <br><span class="text-xs text-slate-500">{{ $customer->email }}</span>
                                        </td>
                                        <td class="py-3 pr-4">{{ $customer->phone ?? '-' }}</td>
                                        <td class="py-3 pr-4">
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Terverifikasi</span>
                                        </td>
                                        <td class="py-3 pr-4">{{ $customer->verifiedBy?->name ?? '-' }}</td>
                                        <td class="py-3">{{ $customer->verified_at?->format('d M Y H:i') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $verifiedCustomers->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
