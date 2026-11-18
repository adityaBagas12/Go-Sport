<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shoeSizes = ['39', '40', '41', '42', '43', '44'];
        $clothesSizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $clothesFemaleSizes = ['S', 'M', 'L', 'XL'];
        $gloveSizes = ['M', 'L', 'XL'];

        $productsConfig = [
            // ===== SEPATU OLAHRAGA =====
            'Sepatu Lari AeroStride' => [
                'sizes' => $shoeSizes,
                'colors' => ['Hitam', 'Putih', 'Navy'],
                'stock_range' => [4, 15],
            ],
            'Sepatu Futsal Indoor Grip' => [
                'sizes' => ['39', '40', '41', '42', '43'],
                'colors' => ['Hitam', 'Putih', 'Merah'],
                'stock_range' => [5, 12],
            ],
            'Sepatu Basket Skyline' => [
                'sizes' => ['40', '41', '42', '43', '44'],
                'colors' => ['Hitam-Merah', 'Putih-Biru'],
                'stock_range' => [3, 10],
            ],
            'Sepatu Badminton Court Flex' => [
                'sizes' => ['39', '40', '41', '42', '43'],
                'colors' => ['Putih', 'Navy', 'Kuning'],
                'stock_range' => [6, 14],
            ],
            'Sepatu Trail Ridge Run' => [
                'sizes' => $shoeSizes,
                'colors' => ['Hitam-Abu', 'Oranye-Hitam'],
                'stock_range' => [4, 10],
            ],

            // ===== JERSEY =====
            'Jersey Match Home' => [
                'sizes' => $clothesSizes,
                'colors' => ['Merah', 'Putih'],
                'stock_range' => [8, 20],
            ],
            'Jersey Match Away' => [
                'sizes' => $clothesSizes,
                'colors' => ['Putih', 'Hitam'],
                'stock_range' => [6, 18],
            ],
            'Jersey Training Airflow' => [
                'sizes' => $clothesSizes,
                'colors' => ['Hitam', 'Navy', 'Abu-abu'],
                'stock_range' => [10, 25],
            ],

            // ===== BAJU & CELANA OLAHRAGA =====
            'Kaos Training Move' => [
                'sizes' => $clothesSizes,
                'colors' => ['Hitam', 'Putih', 'Navy'],
                'stock_range' => [8, 22],
            ],
            'Celana Jogger Active' => [
                'sizes' => $clothesSizes,
                'colors' => ['Hitam', 'Abu-abu', 'Navy'],
                'stock_range' => [6, 16],
            ],
            'Legging Motion High Rise' => [
                'sizes' => $clothesFemaleSizes,
                'colors' => ['Hitam', 'Navy', 'Maroon'],
                'stock_range' => [5, 15],
            ],

            // ===== BOLA =====
            'Bola Sepak Match Pro Size 5' => [
                'sizes' => null,
                'colors' => ['Putih-Hitam', 'Putih-Biru'],
                'stock_range' => [15, 25],
            ],
            'Bola Basket Street Grip' => [
                'sizes' => null,
                'colors' => ['Coklat Klasik', 'Hitam'],
                'stock_range' => [12, 20],
            ],
            'Bola Voli Rally Touch' => [
                'sizes' => null,
                'colors' => ['Kuning-Biru', 'Putih-Merah'],
                'stock_range' => [10, 18],
            ],

            // ===== RAKET =====
            'Raket Badminton Carbon Strike' => [
                'sizes' => null,
                'colors' => ['Hitam-Emas', 'Merah-Hitam'],
                'stock_range' => [10, 20],
            ],
            'Raket Tenis Baseline Pro' => [
                'sizes' => null,
                'colors' => ['Hitam-Hijau', 'Biru-Putih'],
                'stock_range' => [6, 12],
            ],

            // ===== TAS OLAHRAGA =====
            'Duffel Bag Training 40L' => [
                'sizes' => null,
                'colors' => ['Hitam', 'Navy', 'Abu-abu'],
                'stock_range' => [8, 15],
            ],
            'Ransel Gym Core' => [
                'sizes' => null,
                'colors' => ['Hitam', 'Army', 'Navy'],
                'stock_range' => [10, 18],
            ],
            'Tas Raket Double Zone' => [
                'sizes' => null,
                'colors' => ['Hitam-Merah', 'Navy-Putih'],
                'stock_range' => [8, 15],
            ],

            // ===== AKSESORIS OLAHRAGA =====
            'Botol Minum Hydrate 1L' => [
                'sizes' => null,
                'colors' => ['Hitam Matte', 'Biru Navy', 'Hijau Sage', 'Ungu Pastel'],
                'stock_range' => [20, 35],
            ],
            'Sarung Tangan Training Grip' => [
                'sizes' => $gloveSizes,
                'colors' => ['Hitam', 'Merah'],
                'stock_range' => [8, 16],
            ],
            'Kaos Kaki Active 3 Pasang' => [
                'sizes' => null,
                'colors' => ['Hitam', 'Putih', 'Abu-abu'],
                'stock_range' => [25, 40],
            ],

            // ===== PERALATAN FITNESS =====
            'Matras Yoga Balance 6mm' => [
                'sizes' => null,
                'colors' => ['Ungu', 'Tosca', 'Abu-abu', 'Hitam'],
                'stock_range' => [15, 25],
            ],
            'Speed Rope Training' => [
                'sizes' => null,
                'colors' => ['Hitam', 'Merah', 'Biru'],
                'stock_range' => [20, 35],
            ],
        ];

        foreach ($productsConfig as $productName => $config) {
            $product = Product::where('name', $productName)->first();

            if (! $product) {
                continue;
            }

            // Hapus varian lama
            $product->variants()->delete();

            $sizes = $config['sizes'] ?? null;
            $colors = $config['colors'] ?? null;
            $stockRange = $config['stock_range'] ?? [5, 15];

            $sizeList = ! empty($sizes) ? $sizes : [null];
            $colorList = ! empty($colors) ? $colors : [null];

            $variantData = [];
            $counter = 0;

            foreach ($sizeList as $size) {
                foreach ($colorList as $color) {
                    $counter++;
                    // Simulasikan variasi stok realistis (beberapa stok banyak, sesekali stok sedikit)
                    $variantStock = rand($stockRange[0], $stockRange[1]);

                    // Pada varian tertentu buat stok rendah atau habis (0) untuk pengujian UI
                    if ($counter === 3 && $productName === 'Sepatu Lari AeroStride') {
                        $variantStock = 0; // Habis
                    } elseif ($counter === 7 && $productName === 'Sepatu Lari AeroStride') {
                        $variantStock = 2; // Sisa sedikit
                    }

                    $skuParts = [strtoupper(Str::slug($product->name))];
                    if ($size) {
                        $skuParts[] = strtoupper(Str::slug($size));
                    }
                    if ($color) {
                        $skuParts[] = strtoupper(Str::slug($color));
                    }

                    $variantData[] = [
                        'product_id' => $product->id,
                        'size' => $size,
                        'color' => $color,
                        'stock' => $variantStock,
                        'price' => null,
                        'sku' => implode('-', $skuParts),
                    ];
                }
            }

            $product->variants()->createMany($variantData);

            // Update produk utama agar stok dan pilihan varian sinkron
            $product->update([
                'stock' => $product->variants()->sum('stock'),
                'sizes' => $sizes,
                'colors' => $colors,
            ]);
        }
    }
}
