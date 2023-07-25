<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'product_number' => fake()->numberBetween(1000000, 9999999),
            'oms_1' => fake()->name(),
            'oms_2' => fake()->name(),
            'oms_3' => fake()->name(),
            'search_name' => fake()->name(),
            'ean_number' => fake()->numberBetween(1000000, 9999999),
            'sell_price' => fake()->randomDigit(),
            'unit' => fake()->name(),
            'unit_price' => fake()->randomDigit(),
            'stock' => fake()->randomDigit(),
        ];
    }
}
