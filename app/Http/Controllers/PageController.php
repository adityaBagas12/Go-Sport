<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
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
        $coupons = Coupon::where('is_active', true)->latest()->get();

        $products = Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where(function ($sub) {
                    $sub->whereNotNull('compare_price')
                        ->whereColumn('compare_price', '>', 'price');
                })->orWhere('badge', 'promo');
            })
            ->orderByDesc('sold')
            ->paginate(12);

        $maxDiscount = $coupons->where('type', 'percent')->max('value');

        return view('pages.promo', compact('products', 'coupons', 'maxDiscount'));
    }
}
