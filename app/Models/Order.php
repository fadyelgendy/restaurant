<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const TAX = 10;

    protected $fillable = [
        'merchant_id',
        'order_number',
        'sub_total',
        'tax',
        'total',
        'status'
    ];

    protected $hidden = [
        "created_at",
        "updated_at"
    ];

    protected $with = ['orderProducts'];

    public static function calculateSubTotal(array $products): float
    {
        $subTotal = 0;

        foreach ($products as $product) {
            $productModel = Product::find($product['product_id']);
            $subTotal += $productModel->price * $product['quantity'];
        }

        return $subTotal;
    }

    public static function calculateTotal(float $subTotal): float
    {
        return $subTotal + ($subTotal * (self::TAX / 100));
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }
}
