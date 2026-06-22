<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashierController extends Controller
{
    public function index()
    {
        // 1. Ambil data metrik bawaan lo
        $todaySales = Transaction::whereDate('created_at', today())->sum('total') ?? 0;
        $transactionCount = Transaction::whereDate('created_at', today())->count() ?? 0;

        // 2. Tambahkan pengambilan data produk untuk katalog kasir
        $products = Product::all();

        // 3. Masukkan 'products' ke dalam compact
        return view('cashier.index', compact('todaySales', 'transactionCount', 'products'));
    }

    public function getProducts()
    {
        return response()->json(Product::where('stock', '>', 0)->get());
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'payment_method' => 'required|string',
            'total' => 'required|integer', // Hanya validasi total yang wajib dari client
        ]);

        // Hitung sendiri di server agar lebih aman dari manipulasi user
        $subtotal = collect($request->cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $tax = (int)($subtotal * 0.11); // Contoh pajak 11%
        $total = $subtotal + $tax;

        \Log::info('Data Checkout:', $request->all());

        DB::beginTransaction();
        try {
            // Gunakan variabel hasil hitung server
            $transaction = Transaction::create([
                'transaction_code' => 'TXS-' . strtoupper(Str::random(8)),
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total
            ]);

            foreach ($request->cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $product->decrement('stock', $item['quantity']);

                // ATTACH ITEM KE TABEL PIVOT + KUNCI HARGA SAAT INI
                $transaction->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price_at_transaction' => $product->price // Harga historis terkunci
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transaction processed successfully!',
                'code' => $transaction->transaction_code
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
