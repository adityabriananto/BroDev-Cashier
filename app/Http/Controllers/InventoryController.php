<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Services\InventoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Apply manual stock adjustment.
     */
    public function adjust(StockAdjustmentRequest $request): JsonResponse
    {
        try {
            $product = $this->inventoryService->adjustStock(
                (int) $request->product_id,
                (int) $request->quantity,
                (string) $request->reason
            );

            return $this->successResponse(
                [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'stock' => $product->stock,
                ],
                'Stock adjusted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get list of low stock products.
     */
    public function lowStock(): JsonResponse
    {
        $products = $this->inventoryService->getLowStockProducts();

        return $this->successResponse($products, 'Low stock products retrieved successfully');
    }
}
