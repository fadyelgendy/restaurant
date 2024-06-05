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
        \App\Models\User::factory(1)->create([
            'email' => 'customer@mail.com',
            'role' => \App\Enums\Role::CUSTOMER->value
        ]);

        $beef = \App\Models\Ingredient::factory()->create(['name' => 'Beef', 'initial' => 20000, 'stock' => 20000]);
        $cheese = \App\Models\Ingredient::factory()->create(['name' => 'Cheese', 'initial' => 5000, 'stock' => 5000]);
        $onion = \App\Models\Ingredient::factory()->create(['name' => 'Onion', 'initial' => 1000, 'stock' => 1000]);

        $product = $merchant->products()->create(['name' => 'Burger', 'price' => 150, 'quantity' => 100]);

        $product->productIngredients()->createMany([
            ['ingredient_id' => $beef->id, 'quantity' => 150],
            ['ingredient_id' => $cheese->id, 'quantity' => 30],
            ['ingredient_id' => $onion->id, 'quantity' => 20]
        ]);
    }
}
