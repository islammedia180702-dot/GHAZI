<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = User::query()
            ->where('role', UserRole::CUSTOMER->value)
            ->where('verification_status', 'verified')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim($request->string('search')->toString());

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create', ['customer' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        unset($validated['password_confirmation']);

        $customer = User::query()->create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'role' => UserRole::CUSTOMER,
            'verification_status' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', "Akun pelanggan {$customer->name} berhasil ditambahkan.");
    }

    public function show(User $customer): View
    {
        $this->ensureVerifiedCustomer($customer);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(User $customer): View
    {
        $this->ensureVerifiedCustomer($customer);

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer): RedirectResponse
    {
        $this->ensureVerifiedCustomer($customer);

        $validated = $request->validate($this->rules($customer));
        unset($validated['password_confirmation']);

        if (filled($validated['password'] ?? null)) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', "Akun pelanggan {$customer->name} berhasil diperbarui.");
    }

    public function destroy(User $customer): RedirectResponse
    {
        $this->ensureVerifiedCustomer($customer);

        if ($customer->rentals()->exists()) {
            return back()->withErrors([
                'customer' => 'Akun tidak dapat dihapus karena memiliki riwayat penyewaan.',
            ]);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Akun pelanggan berhasil dihapus.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?User $customer = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($customer),
            ],
            'phone' => ['required', 'string', 'min:8', 'max:32'],
            'address' => ['required', 'string', 'min:10', 'max:2000'],
            'city' => ['required', 'string', 'max:255'],
            'kecamatan' => ['required', 'string', 'max:255'],
            'password' => [$customer === null ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    private function ensureVerifiedCustomer(User $customer): void
    {
        abort_unless(
            $customer->isCustomer() && $customer->verification_status === 'verified',
            404
        );
    }
}
