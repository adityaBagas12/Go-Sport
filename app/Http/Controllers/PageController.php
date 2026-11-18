<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

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

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        if ($request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        ContactMessage::create($validated);

        return back()->with('success', 'Terima kasih! Pesan kamu telah berhasil terkirim. Anda dapat memantau jawaban admin langsung di menu Akun > Pesan & Bantuan.');
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
