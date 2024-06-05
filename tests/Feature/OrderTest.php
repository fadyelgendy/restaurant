<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

     /**
     * A basic feature test example.
     */
    public function test_unauthenticated_user_cannot_access_products(): void
    {
        $response = $this->get('api/products');

        $response->assertRedirect('api-login');
    }

    /**
     * A basic feature test example.
     */
    public function test_authenticated_user_only_access_products(): void
    {
        $user = User::factory()->create(['role' =>  \App\Enums\Role::CUSTOMER->value]);

        $response = $this->actingAs($user)->get('api/products');

        $response->assertStatus(200);
    }
}
