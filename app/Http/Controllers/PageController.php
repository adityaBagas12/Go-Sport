<?php

namespace App\Http\Controllers;

use App\Models\Product;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function promo()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->whereNotNull('compare_price')
            ->whereColumn('compare_price', '>', 'price')
            ->orderByDesc('sold')
            ->paginate(12);

        return view('pages.promo', compact('products'));
    }
}
