<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->when($request->filled('badge'), function ($query) use ($request) {
                $query->where('badge', $request->badge);
            })
            ->when($request->filled('min_price'), function ($query) use ($request) {
                $query->where('price', '>=', (int) $request->min_price);
            })
            ->when($request->filled('max_price'), function ($query) use ($request) {
                $query->where('price', '<=', (int) $request->max_price);
            })
            ->when($request->filled('sort'), function ($query) use ($request) {
                match ($request->sort) {
                    'price_asc' => $query->orderBy('price'),
                    'price_desc' => $query->orderByDesc('price'),
                    'newest' => $query->orderByDesc('created_at'),
                    'best' => $query->orderByDesc('sold'),
                    default => $query->orderByDesc('sold')->orderBy('name'),
                };
            }, function ($query) {
                $query->orderByDesc('sold')->orderBy('name');
            })
            ->paginate(12)
            ->withQueryString();

        $categories = Category::withCount('products')->get();

        $selectedCategory = $request->filled('category')
            ? Category::find($request->category)
            : null;

        return view('products.index', compact('products', 'categories', 'selectedCategory'));
    }

    public function show(Request $request, $slug)
    {
        $product = Product::with(['category', 'images', 'reviews'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Product::with('category')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}
