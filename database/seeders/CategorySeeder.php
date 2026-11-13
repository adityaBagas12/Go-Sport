<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Sepatu Olahraga', 'icon' => '👟', 'description' => 'Koleksi sepatu untuk lari, futsal, basket, dan olahraga lainnya.', 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Jersey', 'icon' => '⚽', 'description' => 'Jersey tim, custom, dan training dari bahan premium.', 'image' => 'https://images.unsplash.com/photo-1517466787929-bc90951d0974?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Baju & Celana Olahraga', 'icon' => '🧥', 'description' => 'Pakaian olahraga nyaman untuk pria, wanita, dan anak.', 'image' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Bola', 'icon' => '🏀', 'description' => 'Bola sepak, basket, voli, dan futsal berkualitas.', 'image' => 'https://images.unsplash.com/photo-1614632537190-23e4146777db?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Raket', 'icon' => '🏸', 'description' => 'Raket bulu tangkis dan tenis untuk semua level.', 'image' => 'https://contents.mediadecathlon.com/m25723803/k%243653949da8b686c117f2501e51a2ed3a/picture.jpg'],
            ['name' => 'Tas Olahraga', 'icon' => '🎒', 'description' => 'Tas, ransel, dan duffel bag untuk aktivitas olahraga.', 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Aksesoris Olahraga', 'icon' => '⌚', 'description' => 'Botol minum, sarung tangan, kaos kaki, dan aksesoris lainnya.', 'image' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Peralatan Fitness', 'icon' => '🏋️', 'description' => 'Dumbbell, matras yoga, skipping, dan alat fitness rumahan.', 'image' => 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&w=900&q=80'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    ...$category,
                ]
            );
        }
    }
}
