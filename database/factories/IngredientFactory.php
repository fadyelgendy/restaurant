<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'initial' => fake()->numberBetween(1000, 10000),
            'stock' => fake()->numberBetween(1000, 10000),
            'consumed' => 0,
            'status' => \App\Enums\Status::AVAILABLE->value
        ];
    }
}
