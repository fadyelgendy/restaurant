<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $merchant = \App\Models\User::factory(1)->create(['role' => \App\Enums\Role::MERCHANT->value])->first();
        \App\Models\User::factory(1)->create(['role' => \App\Enums\Role::CUSTOMER->value]);

        $beef = \App\Models\Ingredient::create([
            'name' => 'Beef',
            'initial' => 20000,
            'stock' => 20000,
            'consumed' => 0,
            'status' => \App\Enums\Status::AVAILABLE->value
        ]);

        $cheese = \App\Models\Ingredient::create([
            'name' => 'Cheese',
            'initial' => 5000,
            'stock' => 5000,
            'consumed' => 0,
            'status' => \App\Enums\Status::AVAILABLE->value
        ]);

        $onion = \App\Models\Ingredient::create([
            'name' => 'Onion',
            'initial' => 1000,
            'stock' => 1000,
            'consumed' => 0,
            'status' => \App\Enums\Status::AVAILABLE->value
        ]);

        $product = \App\Models\Product::create([
            'merchant_id' => $merchant->id,
            'name' => 'Burger',
            'price' => 100,
            'quantity' => 5
        ]);

        \App\Models\ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $beef->id,
            'quantity' => 2500
        ]);

        \App\Models\ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $cheese->id,
            'quantity' => 30
        ]);

        \App\Models\ProductIngredient::create([
            'product_id' => $product->id,
            'ingredient_id' => $onion->id,
            'quantity' => 20
        ]);
    }
}
