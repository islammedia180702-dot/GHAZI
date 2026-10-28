<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');

        $products = Product::query()
            ->with(['category', 'images'])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim($request->string('search')->toString());

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['active', 'inactive'], true), function (Builder $query) use ($status): void {
                $query->where('is_active', $status === 'active');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'product' => new Product(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $image = $validated['image'] ?? null;
        unset($validated['image']);

        $product = DB::transaction(function () use ($validated, $image): Product {
            $product = Product::create($validated);

            if ($image !== null) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $image->store('products', 'public'),
                    'is_primary' => true,
                    'alt_text' => $product->name,
                ]);
            }

            return $product;
        });

        return redirect()->route('admin.products.index')
            ->with('success', "Produk {$product->name} berhasil ditambahkan.");
    }

    public function edit(Product $product): View
    {
        $product->load('images');

        return view('admin.products.edit', [
            'categories' => Category::query()->orderBy('name')->get(),
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate($this->rules($product));
        $image = $validated['image'] ?? null;
        unset($validated['image']);
        $previousImage = null;

        DB::transaction(function () use ($validated, $image, $product, &$previousImage): void {
            $product->update($validated);

            if ($image !== null) {
                $primaryImage = $product->images()->where('is_primary', true)->first();
                $path = $image->store('products', 'public');

                if ($primaryImage !== null) {
                    $previousImage = $primaryImage->path;
                    $primaryImage->update([
                        'path' => $path,
                        'alt_text' => $product->name,
                    ]);
                } else {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'is_primary' => true,
                        'alt_text' => $product->name,
                    ]);
                }
            }
        });

        if ($previousImage !== null) {
            Storage::disk('public')->delete($previousImage);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Produk {$product->name} berhasil diperbarui.");
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Product $product = null): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('products', 'slug')->ignore($product),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'price_per_day' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'damage_fee' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'stock_total' => ['required', 'integer', 'min:0', 'max:10000'],
            'is_active' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:3072'],
        ];
    }
}
