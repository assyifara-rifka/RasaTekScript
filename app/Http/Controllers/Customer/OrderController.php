<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        $total = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        return view('customer.checkout', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|min:10|max:15|regex:/^[0-9+\-\s()]+$/',
            'customer_address' => 'required|string',
            'payment_method' => 'required|in:tunai,e-wallet',
            'e_wallet_type' => 'required_if:payment_method,e-wallet|in:dana,gopay,shopeepay',
            'notes' => 'nullable|string'
        ], [
            'customer_phone.min' => 'Nomor telepon minimal 10 digit',
            'customer_phone.max' => 'Nomor telepon maksimal 15 digit',
            'customer_phone.regex' => 'Nomor telepon hanya boleh berisi angka, +, -, spasi, dan tanda kurung'
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Buat order
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $total,
                'status' => 'pending',
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'notes' => $validated['notes'] ?? null,
                'payment_method' => $validated['payment_method'],
                'e_wallet_type' => $validated['e_wallet_type'] ?? null
            ]);

            // Buat order items dan kurangi stok
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Kurangi stok
                $product = Product::find($item['id']);
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // Kosongkan keranjang
            session()->forget('cart');

            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('orderItems.product')
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Pastikan order milik user yang login
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('orderItems.product');
        return view('customer.orders.show', compact('order'));
    }
}