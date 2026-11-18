<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedBigInteger('price')->nullable();
            $table->string('sku')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'size', 'color']);
        });

        // Migrasi data varian awal untuk produk yang sudah ada
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $sizes = json_decode($product->sizes ?? '[]', true) ?: [];
            $colors = json_decode($product->colors ?? '[]', true) ?: [];

            if (! empty($sizes) || ! empty($colors)) {
                $combinations = [];

                if (! empty($sizes) && ! empty($colors)) {
                    foreach ($sizes as $s) {
                        foreach ($colors as $c) {
                            $combinations[] = ['size' => $s, 'color' => $c];
                        }
                    }
                } elseif (! empty($sizes)) {
                    foreach ($sizes as $s) {
                        $combinations[] = ['size' => $s, 'color' => null];
                    }
                } else {
                    foreach ($colors as $c) {
                        $combinations[] = ['size' => null, 'color' => $c];
                    }
                }

                $count = count($combinations);
                if ($count > 0) {
                    $baseStock = (int) floor($product->stock / $count);
                    $remainder = $product->stock % $count;

                    $rows = [];
                    foreach ($combinations as $index => $comb) {
                        $stockForComb = $baseStock + ($index < $remainder ? 1 : 0);
                        $rows[] = [
                            'product_id' => $product->id,
                            'size' => $comb['size'],
                            'color' => $comb['color'],
                            'stock' => $stockForComb,
                            'price' => null,
                            'sku' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    DB::table('product_variants')->insert($rows);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
