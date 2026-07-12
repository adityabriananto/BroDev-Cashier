<?php

namespace App\Http\Controllers;

use App\Actions\Sales\CompleteSaleAction;
use App\Http\Requests\CheckoutRequest;
use App\Models\Transaction;
use App\Services\ProductService;
use App\Traits\ApiResponse;

class CashierController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ProductService $productService,
        protected CompleteSaleAction $completeSaleAction
    ) {}

    public function index()
    {
        // 1. Ambil data metrik bawaan lo
        $todaySales = Transaction::whereDate('created_at', today())->sum('total') ?? 0;
        $transactionCount = Transaction::whereDate('created_at', today())->count() ?? 0;

        // 2. Tambahkan pengambilan data produk untuk katalog kasir
        $products = $this->productService->getActiveProducts();

        // 3. Masukkan 'products' ke dalam compact
        return view('cashier.index', compact('todaySales', 'transactionCount', 'products'));
    }

    public function getProducts()
    {
        return $this->successResponse($this->productService->getActiveProducts(), 'Active products retrieved successfully');
    }

    public function checkout(CheckoutRequest $request)
    {
        \Log::info('Data Checkout:', $request->all());

        try {
            $transaction = $this->completeSaleAction->execute(
                $request->cart,
                $request->payment_method
            );

            return $this->successResponse([
                'code' => $transaction->transaction_code,
            ], 'Transaction processed successfully!');
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                400,
                null,
                str_contains(strtolower($e->getMessage()), 'stock') ? 'insufficient_stock' : null
            );
        }
    }
}
