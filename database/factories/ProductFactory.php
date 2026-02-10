<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            'Pro Controller', 'Wireless Headphones', 'Mechanical Keyboard', 'Gaming Mouse',
            'Denim Jacket', 'Cotton T-Shirt', 'Leather Boots', 'Running Shoes',
            'Science Fiction Novel', 'History Book', 'Cooking Guide', 'Art Album',
            'Garden Shovel', 'Indoor Plant', 'Outdoor Bench', 'Lawn Mower'
        ];

        return [
            'category_id' => \App\Models\Category::factory(),
            'name' => $this->faker->randomElement($products) . ' ' . $this->faker->unique()->numberBetween(100, 999),
            'description' => $this->faker->paragraph(1),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock' => $this->faker->numberBetween(0, 100),
            'enabled' => $this->faker->boolean(80),
        ];
    }
}