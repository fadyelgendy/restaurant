<?php

namespace App\Traits;

use App\Jobs\SendLowStockMailJob;
use App\Models\Order;
use App\Models\Product;

trait OrderTrait
{
    public function isStockAvailableFor(array $products): bool
    {
        foreach ($products as $product) {
            $productModel = Product::where('id', $product['product_id'])->where('quantity', '>=', $product['quantity'])->first();
            if (!$productModel) return false;

            foreach ($productModel->productIngredients as $productIngredient) {
                $ingredientAvailable = $productIngredient->ingredient->stock >= ($productIngredient->quantity * $product['quantity']);
                if (!$ingredientAvailable) return false;
            }
        }

        return true;
    }

    public function updateStock(Order $order): void
    {
        foreach ($order->orderProducts as $orderProduct) {
            $product = $orderProduct->product;
            $product->update(['quantity' => $product->quantity - $orderProduct->quantity]);

            foreach ($product->productIngredients as $productIngredient) {
                $ingredient = $productIngredient->ingredient;
                $ingredient->update([
                    'stock' => $ingredient->stock - ($productIngredient->quantity * $orderProduct->quantity),
                    'consumed' => $ingredient->consumed + ($productIngredient->quantity * $orderProduct->quantity),
                ]);

                if ($ingredient->lowStockReached() && $ingredient->isAvailable()) {
                    SendLowStockMailJob::dispatch($ingredient, $product->merchant);
                    $ingredient->update(['status' => \App\Enums\Status::WARNING->value]);
                }
            }
        }
    }
}
