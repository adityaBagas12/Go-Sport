<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        $latestProducts = Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $bestSellers = Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->orderByDesc('sold')
            ->take(8)
            ->get();

        $promoProducts = Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNotNull('compare_price')
                    ->whereColumn('compare_price', '>', 'price');
            })
            ->orderByDesc('sold')
            ->take(8)
            ->get();

        $featured = Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderByDesc('sold')
            ->take(4)
            ->get();

        return view('home', compact('categories', 'latestProducts', 'bestSellers', 'promoProducts', 'featured'));
    }
}
