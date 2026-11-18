<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->orderByDesc('sold')
            ->paginate(12);

        return view('categories.show', compact('category', 'products'));
    }
}
