<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Setting;
use App\Services\CheckoutCalculator;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CheckoutCalculator $checkoutCalculator) {}

    protected function getCart(): array
    {
        return session('cart', []);
    }

    protected function saveCart(array $cart): void
    {
        session()->put('cart', $cart);
    }

    /**
     * Resolve the products in the cart in a single query (avoids N+1).
     *
     * @return array<int, array{product: Product, quantity: int}>
     */
    protected function cartItems(): array
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return [];
        }

        $productIds = array_keys($cart);

        $products = Product::whereIn('id', $productIds)->where('is_active', true)->get()->keyBy('id');

        $items = [];

        foreach ($productIds as $productId) {
            $product = $products->get($productId);

            if (! $product) {
                continue;
            }

            $items[] = [
                'product' => $product,
                'quantity' => max(1, (int) ($cart[$productId]['quantity'] ?? 1)),
            ];
        }

        return $items;
    }

    public function index(): View
    {
        $items = $this->cartItems();

        $subtotal = collect($items)->sum(
            fn (array $item) => $item['product']->price_per_day * $item['quantity']
        );

        return view('cart.index', compact('items', 'subtotal'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.$product->stock_total],
        ]);

        $quantity = max(1, (int) ($request->input('quantity', 1)));

        if ($product->stock_total < $quantity) {
            return back()->withErrors([
                'quantity' => 'Stok produk tidak mencukupi untuk jumlah yang diminta.',
            ]);
        }

        $cart = $this->getCart();
        $existing = (int) ($cart[$product->id]['quantity'] ?? 0);
        $newQuantity = $existing + $quantity;

        if ($newQuantity > $product->stock_total) {
            return back()->withErrors([
                'quantity' => 'Jumlah total di keranjang melebihi stok yang tersedia.',
            ]);
        }

        $cart[$product->id] = [
            'product_id' => $product->id,
            'quantity' => $newQuantity,
        ];

        $this->saveCart($cart);

        return redirect()->route('cart.index')->with('success', $product->name.' berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $quantity = max(1, (int) $request->input('quantity', 1));

        if ($quantity > $product->stock_total) {
            return back()->withErrors([
                'quantity' => 'Stok produk tidak mencukupi untuk jumlah yang diminta.',
            ]);
        }

        $cart = $this->getCart();
        $cart[$product->id] = [
            'product_id' => $product->id,
            'quantity' => $quantity,
        ];

        $this->saveCart($cart);

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = $this->getCart();
        unset($cart[$product->id]);
        $this->saveCart($cart);

        return redirect()->route('cart.index')->with('success', $product->name.' dihapus dari keranjang.');
    }

    public function checkout(): View|RedirectResponse
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('info', 'Keranjang Anda masih kosong.');
        }

        $items = $this->cartItems();

        if (empty($items)) {
            session()->forget('cart');

            return redirect()->route('cart.index')->with('info', 'Keranjang Anda tidak memiliki produk yang valid.');
        }

        $subtotal = collect($items)->sum(
            fn (array $item) => $item['product']->price_per_day * $item['quantity']
        );

        return view('checkout.index', [
            'user' => auth()->user(),
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }

    public function storeCheckout(Request $request): RedirectResponse
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('catalog')->with('info', 'Keranjang Anda masih kosong.');
        }

        $user = $request->user();

        if ($user->isCustomer() && (
            blank($user->phone)
            || blank($user->address)
            || blank($user->city)
            || blank($user->kecamatan)
        )) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Lengkapi nomor telepon dan alamat lengkap di profil sebelum membuat pesanan.');
        }

        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'pickup_method' => ['required', 'in:pickup,delivery'],
            'address' => ['required_if:pickup_method,delivery', 'nullable', 'string', 'max:255'],
            'delivery_rt_rw' => ['required_if:pickup_method,delivery', 'nullable', 'string', 'max:20'],
            'delivery_house_number' => ['required_if:pickup_method,delivery', 'nullable', 'string', 'max:50'],
            'delivery_latitude' => ['required_if:pickup_method,delivery', 'nullable', 'numeric', 'between:-90,90'],
            'delivery_longitude' => ['required_if:pickup_method,delivery', 'nullable', 'numeric', 'between:-180,180'],
        ]);

        if ($user->verification_status !== 'verified') {
            return redirect()->route('verification.index')->with('warning', 'Verifikasi identitas belum selesai.');
        }

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $this->checkoutCalculator->daysBetween($startDate, $endDate);

        $rental = DB::transaction(function () use (
            $cart,
            $user,
            $validated,
            $startDate,
            $endDate,
            $days
        ): ?Rental {
            Product::query()
                ->whereKey(array_keys($cart))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $errorField = null;
            $errorMessage = null;
            $lineItems = $this->checkoutCalculator->lineItems(
                $cart,
                $days,
                $startDate,
                $endDate,
                $errorField,
                $errorMessage
            );

            if ($errorField !== null) {
                throw ValidationException::withMessages([$errorField => $errorMessage]);
            }

            if (empty($lineItems)) {
                return null;
            }

            $subtotal = array_sum(array_column($lineItems, 'subtotal'));
            $deliveryFee = 0;
            $grandTotal = $subtotal + $deliveryFee;

            $rental = Rental::create([
                'user_id' => $user->id,
                'order_number' => 'GZ-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
                'status' => 'pending',
                'pickup_method' => $validated['pickup_method'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'delivery_rt_rw' => $validated['pickup_method'] === 'delivery' ? $validated['delivery_rt_rw'] : null,
                'delivery_house_number' => $validated['pickup_method'] === 'delivery' ? $validated['delivery_house_number'] : null,
                'delivery_latitude' => $validated['pickup_method'] === 'delivery' ? $validated['delivery_latitude'] : null,
                'delivery_longitude' => $validated['pickup_method'] === 'delivery' ? $validated['delivery_longitude'] : null,
                'grand_total' => $grandTotal,
                'notes' => $validated['address'] ?? null,
            ]);

            foreach ($lineItems as $lineItem) {
                RentalItem::create([
                    'rental_id' => $rental->id,
                    'product_id' => $lineItem['product_id'],
                    'quantity' => $lineItem['quantity'],
                    'unit_price' => $lineItem['unit_price'],
                    'days' => $lineItem['days'],
                    'subtotal' => $lineItem['subtotal'],
                ]);
            }

            Payment::create([
                'rental_id' => $rental->id,
                'amount' => $grandTotal,
                'method' => 'manual',
                'status' => 'pending',
                'reference' => 'PAY-'.$rental->id.'-'.Str::upper(Str::random(8)),
                'expires_at' => now()->addMinutes((int) (Setting::query()->value('pending_expiry_minutes') ?? 15)),
            ]);

            return $rental;
        });

        if ($rental === null) {
            return redirect()->route('cart.index')->with('info', 'Keranjang Anda tidak memiliki produk yang valid.');
        }

        $request->session()->forget('cart');

        return redirect()->route('orders.show', $rental->order_number)
            ->with('success', 'Pesanan berhasil dibuat. Silakan lihat status pembayaran pada detail pesanan.');
    }
}
