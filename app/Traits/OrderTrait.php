<?php

namespace App\Traits;

use App\Jobs\SendLowStockMailJob;
use App\Models\Ingredient;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

trait OrderTrait
{
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

                if ($this->levelExceeded($ingredient) && $ingredient->status === \App\Enums\Status::AVAILABLE->value) {
                    SendLowStockMailJob::dispatch($ingredient, $product->merchant);
                    $ingredient->update(['status' => \App\Enums\Status::WARNING->value]);
                }
            }
        }

        Log::info("Stock updated!");
    }

    protected function levelExceeded(Ingredient $ingredient): bool
    {
        return (($ingredient->consumed * 100) / $ingredient->initial) >= 50;
    }
}
