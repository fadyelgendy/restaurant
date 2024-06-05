<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchant = \App\Models\User::factory()->create(['role' => \App\Enums\Role::MERCHANT->value]);

        $beef = \App\Models\Ingredient::factory()->create(['name' => 'Beef', 'initial' => 20000, 'stock' => 20000, 'consumed' => 0]);
        $cheese = \App\Models\Ingredient::factory()->create(['name' => 'Cheese', 'initial' => 5000, 'stock' => 5000, 'consumed' => 0]);
        $onion = \App\Models\Ingredient::factory()->create(['name' => 'Onion', 'initial' => 1000, 'stock' => 1000, 'consumed' => 0]);

        $product = $merchant->products()->create(['name' => 'Burger', 'price' => 150, 'quantity' => 100]);
        $product->productIngredients()->createMany([
            ['ingredient_id' => $beef->id, 'quantity' => 150],
            ['ingredient_id' => $cheese->id, 'quantity' => 20],
            ['ingredient_id' => $onion->id, 'quantity' => 30],
        ]);
    }
}
