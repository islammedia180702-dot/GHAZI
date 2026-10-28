<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tenda', 'slug' => 'tenda', 'description' => 'Tenda dan perlengkapan berkemah.'],
            ['name' => 'Tas', 'slug' => 'tas', 'description' => 'Tas carrier, daypack, dan aksesoris.'],
            ['name' => 'Tidur', 'slug' => 'tidur', 'description' => 'Sleeping bag, matras, dan hammock.'],
            ['name' => 'Masak', 'slug' => 'masak', 'description' => 'Kompor, nesting, dan gas kaleng.'],
            ['name' => 'Penerangan', 'slug' => 'penerangan', 'description' => 'Headlamp, lampu tenda, dan penerangan.'],
            ['name' => 'Lainnya', 'slug' => 'lainnya', 'description' => 'Aksesoris outdoor dan perlengkapan trekking.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }

        Setting::updateOrCreate([], [
            'store_name' => 'GHAZI OUTDOOR',
            'store_address' => 'Jl. Raya Bandung No. 123, Bandung, Jawa Barat',
            'latitude' => -6.917464,
            'longitude' => 107.619123,
            'whatsapp' => '6281234567890',
            'operational_hours' => '08.00 - 21.00',
            'denda_per_hari' => 25000,
            'pending_expiry_minutes' => 15,
            'free_delivery_km' => 3,
            'fee_per_km' => 5000,
            'max_delivery_km' => 20,
            'terms' => 'Syarat dan ketentuan penyewaan alat outdoor.',
            'privacy_policy' => 'Data pelanggan digunakan untuk kebutuhan penyewaan dan verifikasi identitas.',
        ]);

        User::firstOrCreate(
            ['email' => 'admin@ghazioutdoor.id'],
            [
                'name' => 'Admin GHAZI OUTDOOR',
                'phone' => '081234567890',
                'password' => Hash::make('password123'),
                'role' => UserRole::ADMIN->value,
                'verification_status' => 'verified',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@ghazioutdoor.id'],
            [
                'name' => 'Staff GHAZI OUTDOOR',
                'phone' => '081298765432',
                'password' => Hash::make('password123'),
                'role' => UserRole::STAFF->value,
                'verification_status' => 'verified',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@ghazioutdoor.id'],
            [
                'name' => 'Customer Demo',
                'phone' => '081287654321',
                'password' => Hash::make('password123'),
                'role' => UserRole::CUSTOMER->value,
                'verification_status' => 'verified',
                'email_verified_at' => now(),
                'address' => 'Jl. Cendana No. 17, Bandung',
                'city' => 'Bandung',
                'kecamatan' => 'Coblong',
            ]
        );

        $products = [
            ['category' => 'Tenda', 'name' => 'Dome 2 Orang', 'slug' => 'dome-2-orang', 'price_per_day' => 35000, 'damage_fee' => 200000, 'stock_total' => 5],
            ['category' => 'Tenda', 'name' => 'Dome 4 Orang', 'slug' => 'dome-4-orang', 'price_per_day' => 50000, 'damage_fee' => 250000, 'stock_total' => 6],
            ['category' => 'Tas', 'name' => 'Carrier 40L', 'slug' => 'carrier-40l', 'price_per_day' => 25000, 'damage_fee' => 150000, 'stock_total' => 6],
            ['category' => 'Tidur', 'name' => 'Sleeping Bag', 'slug' => 'sleeping-bag', 'price_per_day' => 15000, 'damage_fee' => 120000, 'stock_total' => 12],
            ['category' => 'Masak', 'name' => 'Kompor Portable', 'slug' => 'kompor-portable', 'price_per_day' => 10000, 'damage_fee' => 90000, 'stock_total' => 8],
            ['category' => 'Penerangan', 'name' => 'Headlamp', 'slug' => 'headlamp', 'price_per_day' => 10000, 'damage_fee' => 75000, 'stock_total' => 10],
            ['category' => 'Lainnya', 'name' => 'Trekking Pole', 'slug' => 'trekking-pole', 'price_per_day' => 10000, 'damage_fee' => 95000, 'stock_total' => 8],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->firstOrFail();
            Product::firstOrCreate(
                ['slug' => $productData['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'description' => 'Produk unggulan GHAZI OUTDOOR untuk kebutuhan outdoor.',
                    'price_per_day' => $productData['price_per_day'],
                    'damage_fee' => $productData['damage_fee'],
                    'stock_total' => $productData['stock_total'],
                    'is_active' => true,
                    'rating_average' => 4.8,
                ]
            );
        }
    }
}
