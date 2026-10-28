<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IdentityVerificationController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('profile.verification', [
            'user' => $user,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nik' => ['required', 'string', 'size:16'],
            'ktp_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'selfie_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'agreement' => ['accepted'],
        ]);

        $user = $request->user();

        $ktpPath = $request->file('ktp_image')->storeAs(
            'private/identities/'.$user->id,
            'ktp_'.time().'.'.$request->file('ktp_image')->extension(),
            'local'
        );

        $selfiePath = $request->file('selfie_image')->storeAs(
            'private/identities/'.$user->id,
            'selfie_'.time().'.'.$request->file('selfie_image')->extension(),
            'local'
        );

        $user->update([
            'nik' => Crypt::encryptString($request->nik),
            'ktp_image' => $ktpPath,
            'selfie_image' => $selfiePath,
            'verification_status' => 'pending',
            'rejection_reason' => null,
        ]);

        return redirect()->route('verification.index')->with('status', 'Berkas identitas Anda telah dikirim untuk diverifikasi.');
    }

    public function adminIndex(): View
    {
        $queue = User::whereIn('verification_status', ['pending', 'rejected'])
            ->orderByDesc('updated_at')
            ->get();
        $verifiedCustomers = User::query()
            ->where('role', UserRole::CUSTOMER->value)
            ->where('verification_status', 'verified')
            ->with('verifiedBy:id,name')
            ->latest('verified_at')
            ->paginate(15, ['id', 'name', 'email', 'phone', 'verified_by', 'verified_at']);

        return view('admin.verifications', [
            'queue' => $queue,
            'verifiedCustomers' => $verifiedCustomers,
        ]);
    }

    public function showFile(User $user, string $type)
    {
        abort_unless(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isStaff()), 403);

        $path = match ($type) {
            'ktp' => $user->ktp_image,
            'selfie' => $user->selfie_image,
            default => null,
        };

        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($path));
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update([
            'verification_status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('status', 'Identitas pelanggan disetujui.');
    }

    public function reject(User $user, Request $request): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:10'],
        ]);

        $user->update([
            'verification_status' => 'rejected',
            'verified_by' => auth()->id(),
            'rejection_reason' => $request->reason,
            'verified_at' => null,
        ]);

        return back()->with('status', 'Identitas pelanggan ditolak.');
    }
}
