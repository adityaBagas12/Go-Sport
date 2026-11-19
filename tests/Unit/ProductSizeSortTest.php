<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductSizeSortTest extends TestCase
{
    public function test_sorts_clothing_sizes_from_smallest_to_largest(): void
    {
        $input = ['M', 'S', 'L', 'XS', 'XL', 'XXL'];
        $expected = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

        $this->assertEquals($expected, Product::sortSizes($input));
    }

    public function test_sorts_shoe_sizes_numerically_ascending(): void
    {
        $input = ['42', '39', '44', '40', '41', '43'];
        $expected = ['39', '40', '41', '42', '43', '44'];

        $this->assertEquals($expected, Product::sortSizes($input));
    }
}
