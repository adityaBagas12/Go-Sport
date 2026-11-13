<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private array $categories;

    public function run(): void
    {
        $this->categories = Category::pluck('id', 'slug')->toArray();
        $categoryImages = Category::pluck('image', 'slug')->toArray();

        $products = [
            // ===== SEPATU OLAHRAGA =====
            ['sepatu-olahraga', 'Sepatu Lari AeroStride', 549000, 699000, 25, 'XL'],
            ['sepatu-olahraga', 'Sepatu Futsal Indoor Grip', 429000, null, 30, 'XL'],
            ['sepatu-olahraga', 'Sepatu Basket Skyline', 789000, 899000, 15, 'XL'],
            ['sepatu-olahraga', 'Sepatu Badminton Court Flex', 389000, null, 40, 'MD'],
            ['sepatu-olahraga', 'Sepatu Trail Ridge Run', 649000, 799000, 18, 'XL'],

            // ===== JERSEY =====
            ['jersey', 'Jersey Match Home', 189000, 249000, 60, null],
            ['jersey', 'Jersey Match Away', 189000, null, 55, null],
            ['jersey', 'Jersey Training Airflow', 149000, null, 80, null],

            // ===== BAJU & CELANA OLAHRAGA =====
            ['baju-celana-olahraga', 'Kaos Training Move', 89000, 109000, 100, null],
            ['baju-celana-olahraga', 'Celana Jogger Active', 119000, null, 90, null],
            ['baju-celana-olahraga', 'Legging Motion High Rise', 129000, 159000, 75, null],

            // ===== BOLA =====
            ['bola', 'Bola Sepak Match Pro Size 5', 159000, 199000, 40, null],
            ['bola', 'Bola Basket Street Grip', 179000, null, 35, null],
            ['bola', 'Bola Voli Rally Touch', 149000, 179000, 30, null],

            // ===== RAKET =====
            ['raket', 'Raket Badminton Carbon Strike', 289000, 349000, 30, null],
            ['raket', 'Raket Tenis Baseline Pro', 629000, null, 15, null],

            // ===== TAS OLAHRAGA =====
            ['tas-olahraga', 'Duffel Bag Training 40L', 179000, 229000, 35, null],
            ['tas-olahraga', 'Ransel Gym Core', 149000, null, 40, null],
            ['tas-olahraga', 'Tas Raket Double Zone', 129000, 159000, 30, null],

            // ===== AKSESORIS OLAHRAGA =====
            ['aksesoris-olahraga', 'Botol Minum Hydrate 1L', 49000, 69000, 120, null],
            ['aksesoris-olahraga', 'Sarung Tangan Training Grip', 79000, null, 60, null],
            ['aksesoris-olahraga', 'Kaos Kaki Active 3 Pasang', 59000, null, 100, null],

            // ===== PERALATAN FITNESS =====
            ['peralatan-fitness', 'Dumbbell Hex 2kg Sepasang', 119000, 149000, 55, null],
            ['peralatan-fitness', 'Matras Yoga Balance 6mm', 99000, null, 80, null],
            ['peralatan-fitness', 'Speed Rope Training', 49000, 69000, 100, null],
            ['peralatan-fitness', 'Barbel Adjustable 20kg', 549000, 649000, 18, null],
        ];

        $sizesOptions = [
            'XL' => ['S', 'M', 'L', 'XL', 'XXL'],
            'MD' => ['M', 'L', 'XL'],
            null => null,
        ];

        $colors = ['Hitam', 'Putih', 'Navy', 'Merah', 'Biru', 'Oranye'];

        $index = 0;

        foreach ($products as [$categorySlug, $name, $price, $comparePrice, $stock, $sizeKey]) {
            $index++;

            $badge = match (true) {
                $index <= 5 => 'terlaris',
                $index % 3 === 0 => 'promo',
                $index % 4 === 0 => null,
                default => 'baru',
            };

            $isFeatured = $index <= 8;
            $sold = fake()->numberBetween(20, 500);
            $rating = fake()->randomFloat(1, 4.3, 5.0);
            $sizes = $sizesOptions[$sizeKey] ?? null;

            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $this->categories[$categorySlug],
                    'name' => $name,
                    'description' => "{$name} dirancang khusus untuk mendukung performa terbaik kamu saat berolahraga. Dibuat dari material berkualitas tinggi yang nyaman dipakai, tahan lama, dan cocok untuk penggunaan harian maupun kompetisi. Produk GoSport dijamin original dan bergaransi.",
                    'specification' => 'Material: polyester/oksford premium. Tersedia dalam berbagai ukuran dan warna. Perawatan mudah, cukup dicuci dengan air dingin.',
                    'price' => $price,
                    'compare_price' => $comparePrice,
                    'stock' => $stock,
                    'rating' => $rating,
                    'sold' => $sold,
                    'sizes' => $sizes,
                    'colors' => $sizes ? array_slice($colors, 0, fake()->numberBetween(2, 5)) : null,
                    'badge' => $badge,
                    'image' => $categoryImages[$categorySlug] ?? null,
                    'is_featured' => $isFeatured,
                    'is_active' => true,
                ]
            );

            $product->images()->delete();
        }
    }
}
