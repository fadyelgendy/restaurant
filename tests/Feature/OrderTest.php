<?php

namespace Tests\Feature;

use App\Jobs\SendLowStockMailJob;
use App\Models\Order;
use App\Models\Product;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_created_onlu_by_customer_user(): void
    {
        $this->seed(ProductSeeder::class);
        
        $merchant = \App\Models\User::factory()->create(['role' => \App\Enums\Role::MERCHANT->value]);
        $product = Product::find(1);

        $response = $this->actingAs($merchant)->post('api/orders', [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(403);
        $response->assertExactJson([
            "status" => 403,
            "errors" => ["error" => "Unauthorized Access!"]
        ]);
    }

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
            'name' => 'Beef',
            'initial' => 10000,
            'stock' => 0,
            'consumed' => 10000,
            'status' => \App\Enums\Status::OUT_OF_STOCK->value
        ]);

        $cheese = \App\Models\Ingredient::factory()->create(['name' => 'Cheese']);
        $onion = \App\Models\Ingredient::factory()->create(['name' => 'Onion']);

        $product = $merchant->products()->create(['name' => 'Burger', 'price' => 100, 'quantity' => 100]);
        $product->productIngredients()->createMany([
            ['ingredient_id' => $beef->id, 'quantity' => 150],
            ['ingredient_id' => $cheese->id, 'quantity' => 10],
            ['ingredient_id' => $onion->id, 'quantity' => 30],
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
        $response->assertExactJson([
            "status" => 400,
            "errors" => ["error" => "Product and/or it's ingredient(s) is Out Of Stock!"]
        ]);
    }

    public function test_order_created_successfully(): void
    {
        $this->seed(ProductSeeder::class);
        
        $user = \App\Models\User::factory()->create(['role' => \App\Enums\Role::CUSTOMER->value]);
        $product = Product::find(1);


        $response = $this->actingAs($user)->post('api/orders', [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_products', 1);

        $response->assertStatus(201);
        $response->assertExactJson([
            "status" => 201,
            "data" => ["message" => "Order created successfully!"]
        ]);
    }

    public function test_order_created_successfully_and_stock_updated(): void
    {
        $this->seed(ProductSeeder::class);
        
        $user = \App\Models\User::factory()->create(['role' => \App\Enums\Role::CUSTOMER->value]);
        $product = Product::find(1);

        $response = $this->actingAs($user)->post('api/orders', [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(201);
        $response->assertExactJson([
            "status" => 201,
            "data" => ["message" => "Order created successfully!"]
        ]);

        $beef = \App\Models\Ingredient::where('name', 'Beef')->first();
        $this->assertEquals($beef->consumed, 300);
        $this->assertEquals($beef->stock, ($beef->initial - $beef->consumed));

        $cheese = \App\Models\Ingredient::where('name', 'Cheese')->first();
        $this->assertEquals($cheese->consumed, 40);
        $this->assertEquals($cheese->stock, $cheese->initial - $cheese->consumed);

        $onion = \App\Models\Ingredient::where('name', 'Onion')->first();
        $this->assertEquals($onion->consumed, 60);
        $this->assertEquals($onion->stock, $onion->initial - $onion->consumed);

        $__product = Product::find($product->id);
        $this->assertEquals($__product->quantity, $product->quantity - 2);
    }

    public function test_order_created_successfully_and_sending_email_job_dispatched_when_reached_fifty_percent_stock(): void
    {
        Queue::fake();

        $user = \App\Models\User::factory()->create(['role' => \App\Enums\Role::CUSTOMER->value]);
        $merchant = \App\Models\User::factory()->create(['role' => \App\Enums\Role::MERCHANT->value]);

        $beef = \App\Models\Ingredient::factory()->create(['name' => 'Beef', 'initial' => 1000, 'stock' => 1000, 'consumed' => 0]);
        $cheese = \App\Models\Ingredient::factory()->create(['name' => 'Cheese', 'initial' => 5000, 'stock' => 5000, 'consumed' => 0]);
        $onion = \App\Models\Ingredient::factory()->create(['name' => 'Onion', 'initial' => 1000, 'stock' => 1000, 'consumed' => 0]);

        $product = $merchant->products()->create(['name' => 'Burger', 'price' => 150, 'quantity' => 100]);
        $product->productIngredients()->createMany([
            ['ingredient_id' => $beef->id, 'quantity' => 250],
            ['ingredient_id' => $cheese->id, 'quantity' => 20],
            ['ingredient_id' => $onion->id, 'quantity' => 30],
        ]);


        $response = $this->actingAs($user)->post('api/orders', [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(201);
        $response->assertExactJson([
            "status" => 201,
            "data" => ["message" => "Order created successfully!"]
        ]);

        $__beef = \App\Models\Ingredient::find($beef->id);
        $this->assertEquals($__beef->consumed, 500);
        $this->assertEquals($__beef->stock, ($beef->initial - $__beef->consumed));

        $__cheese = \App\Models\Ingredient::find($cheese->id);
        $this->assertEquals($__cheese->consumed, 40);
        $this->assertEquals($__cheese->stock, $cheese->initial - $__cheese->consumed);

        $__onion = \App\Models\Ingredient::find($onion->id);
        $this->assertEquals($__onion->consumed, 60);
        $this->assertEquals($__onion->stock, $onion->initial - $__onion->consumed);

        $__product = \App\Models\Product::find($product->id);
        $this->assertEquals($__product->quantity, $product->quantity - 2);

        Queue::assertPushed(SendLowStockMailJob::class);
    }

    public function test_order_created_successfully_and_order_detials_is_correct(): void
    {
        $this->seed(ProductSeeder::class);

        $user = \App\Models\User::factory()->create(['role' => \App\Enums\Role::CUSTOMER->value]);
        $product = Product::find(1);

        $response = $this->actingAs($user)->post('api/orders', [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(201);
        $response->assertExactJson([
            "status" => 201,
            "data" => ["message" => "Order created successfully!"]
        ]);

        $order = $user->orders()->latest()->first();

        $this->assertEquals($order->sub_total, 300);
        $this->assertEquals($order->tax, Order::TAX);
        $this->assertEquals($order->total, ($order->sub_total + ($order->sub_total * (Order::TAX / 100))));
        $this->assertEquals($order->status, \App\Enums\Status::PENDING->value);
    }
}
