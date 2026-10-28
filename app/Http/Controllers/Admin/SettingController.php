<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $setting = Setting::query()->first() ?? new Setting([
            'store_name' => 'GHAZI OUTDOOR',
            'denda_per_hari' => 25000,
            'pending_expiry_minutes' => 15,
            'free_delivery_km' => 3,
            'fee_per_km' => 5000,
            'max_delivery_km' => 20,
        ]);

        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_address' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'whatsapp' => ['nullable', 'string', 'max:32'],
            'operational_hours' => ['nullable', 'string', 'max:255'],
            'denda_per_hari' => ['required', 'integer', 'min:0', 'max:100000000'],
            'pending_expiry_minutes' => ['required', 'integer', 'between:1,10080'],
            'free_delivery_km' => ['required', 'numeric', 'min:0', 'max:1000', 'lte:max_delivery_km'],
            'fee_per_km' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'max_delivery_km' => ['required', 'numeric', 'gt:0', 'max:1000'],
            'terms' => ['nullable', 'string', 'max:20000'],
            'privacy_policy' => ['nullable', 'string', 'max:20000'],
        ]);

        $setting = Setting::query()->first();

        if ($setting === null) {
            Setting::query()->create($validated);
        } else {
            $setting->update($validated);
        }

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
