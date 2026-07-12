<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => 'SKU-'.fake()->unique()->numerify('####'),
            'name' => fake()->words(3, true),
            'price' => fake()->numberBetween(1000, 1000000),
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}
