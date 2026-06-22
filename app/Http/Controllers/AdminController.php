<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index() {
        // Mengambil semua data termasuk yang sudah dihapus (Soft Deleted)
        $products = Product::withTrashed()->orderBy('created_at', 'desc')->get();
        return view('admin.products', compact('products'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'sku' => 'required|unique:products,sku',
            'name' => 'required',
            'price' => 'required|integer',
            'stock' => 'required|integer',
        ]);
        Product::create($data);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);
        $data = $request->validate([
            'sku' => 'required|unique:products,sku,' . $id,
            'name' => 'required',
            'price' => 'required|integer',
            'stock' => 'required|integer',
        ]);
        $product->update($data);
        return response()->json(['success' => true]);
    }

    public function destroy($id) {
        Product::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function restore($id) {
        Product::withTrashed()->findOrFail($id)->restore();
        return response()->json(['success' => true]);
    }

    public function transactionIndex()
    {
        $transactions = Transaction::orderBy('created_at', 'desc')->get();
        return view('admin.transactions', compact('transactions'));
    }

    // API endpoint detail item transaksi + comparison current price
    public function transactionDetail($id)
    {
        // 1. Tambahkan pengecekan jika transaksi tidak ditemukan
        $transaction = Transaction::with(['products' => function($query) {
            $query->withTrashed();
        }])->find($id);

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        return response()->json([
            'success' => true,
            'items' => $transaction->products->map(function($product) {
                // 2. Cek apakah produk sudah benar-benar dihapus (soft delete)
                $isDeleted = !is_null($product->deleted_at);

                return [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $product->pivot->quantity,
                    'price_at_transaction' => (int)$product->pivot->price_at_transaction,
                    // Menggunakan ternary yang lebih aman
                    'current_price' => $isDeleted ? null : (int)$product->price,
                    // Logika perbandingan harga hanya jika produk masih aktif
                    'is_price_changed' => !$isDeleted && ((int)$product->pivot->price_at_transaction !== (int)$product->price)
                ];
            })
        ]);
    }

    public function dashboardIndex()
    {
        // 1. Metrik Hari Ini (Today Metrics)
        $todaySalesRevenue = Transaction::whereDate('created_at', today())->sum('total');

        // Hitung total item yang keluar hari ini via tabel pivot transaction_items
        $todayItemsSold = DB::table('transaction_items')
            ->whereDate('created_at', today())
            ->sum('quantity');

        // 2. Data Tren Analisa 7 Hari Terakhir (Last 7 Days Chart Dataset Alternative)
        $sevenDaysForm = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i)->format('Y-m-d');
            $dateDisplay = today()->subDays($i)->format('d M');

            $sales = Transaction::whereDate('created_at', $date)->sum('total');
            $items = DB::table('transaction_items')->whereDate('created_at', $date)->sum('quantity');

            $sevenDaysForm[] = [
                'date' => $dateDisplay,
                'revenue' => $sales,
                'items_sold' => $items ?? 0
            ];
        }

        return view('admin.dashboard', [
            'todayRevenue' => $todaySalesRevenue,
            'todayItemsSold' => $todayItemsSold ?? 0,
            'trends' => $sevenDaysForm
        ]);
    }
}
