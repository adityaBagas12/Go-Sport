<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->wishlists()->with('product.category')
            ->latest()
            ->get()
            ->filter(fn ($item) => $item->product && $item->product->is_active);

        return view('account.wishlist', compact('items'));
    }

    public function toggle(Request $request, Product $product)
    {
        $user = $request->user();

        if ($user->wishlists()->where('product_id', $product->id)->exists()) {
            $user->wishlists()->where('product_id', $product->id)->delete();

            return back()->with('success', 'Produk dihapus dari wishlist.');
        }

        $user->wishlists()->create(['product_id' => $product->id]);

        return back()->with('success', 'Produk ditambahkan ke wishlist.');
    }
}
