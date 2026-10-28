<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class PagesController extends Controller
{
    public function terms(): View
    {
        $setting = Setting::first();

        return view('pages.terms', [
            'setting' => $setting,
        ]);
    }

    public function privacy(): View
    {
        $setting = Setting::first();

        return view('pages.privacy', [
            'setting' => $setting,
        ]);
    }

    public function contact(): View
    {
        $setting = Setting::first();

        return view('pages.contact', [
            'setting' => $setting,
        ]);
    }
}
