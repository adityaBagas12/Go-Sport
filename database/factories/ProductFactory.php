<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(3, true));
        $price = fake()->numberBetween(50, 1500) * 1000;

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 999999),
            'description' => fake()->paragraph(),
            'specification' => fake()->sentence(),
            'price' => $price,
            'compare_price' => null,
            'stock' => fake()->numberBetween(5, 100),
            'rating' => fake()->randomFloat(1, 4, 5),
            'sold' => 0,
            'sizes' => ['S', 'M', 'L'],
            'colors' => ['Hitam'],
            'badge' => null,
            'image' => null,
            'is_featured' => false,
            'is_active' => true,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
