<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_create_order_without_payload(): void
    {
        $user = \App\Models\User::factory()->create(['role' => \App\Enums\Role::CUSTOMER->value]);

        $response = $this->actingAs($user)->post('api/orders');

        $response->assertStatus(422);

        $response->json([
            "status" => 422,
            "errors" => [
                "products" => ["products is required"],
                "products.0.product_id" => [
                    "The selected products.0.product_id is required."
                ]
            ]
        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_user_cannot_create_order_with_products_dont_exists(): void
    {

        $user = \App\Models\User::factory()->create(['role' => \App\Enums\Role::CUSTOMER->value]);

        $response = $this->actingAs($user)->post('api/orders', [
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->json([
            "status" => 422,
            "errors" => [
                "products.0.product_id" => [
                    "The selected products.0.product_id is invalid."
                ]
            ]
        ]);
    }

    public function test_order_cannot_be_created_with_one_ingredient_is_out_of_stock(): void
    {
        $user = \App\Models\User::factory()->create(['role' => \App\Enums\Role::CUSTOMER->value]);
        $merchant = \App\Models\User::factory()->create(['role' => \App\Enums\Role::MERCHANT->value]);

        $beef = \App\Models\Ingredient::factory()->create([
            'initial' => 10000,
            'stock' => 0,
            'consumed' => 10000,
            'status' => \App\Enums\Status::OUT_OF_STOCK->value
        ]);

        $cheese = \App\Models\Ingredient::factory()->create(['name' => 'Cheese']);
        $onion = \App\Models\Ingredient::factory()->create(['name' => 'Onion']);

        $product = \App\Models\Product::factory()->create(['merchant_id' => $merchant->id,  'name' => 'Burger']);
        $product->productIngredients()->createMany([
            ['product_id' => $product->id, 'ingredient_id' => $beef->id, 'quantity' => 150],
            ['product_id' => $product->id, 'ingredient_id' => $cheese->id, 'quantity' => 10],
            ['product_id' => $product->id, 'ingredient_id' => $onion->id, 'quantity' => 30],
        ]);


        $response = $this->actingAs($user)->post('api/orders', [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(400);
        $response->json([
            "status" => 400,
            "errors" => [
                "products.0.product_id" => [
                    "The selected products.0.product_id is invalid."
                ]
            ]
        ]);
    }
}
