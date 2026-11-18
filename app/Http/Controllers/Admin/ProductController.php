<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'variants'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'specification' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'compare_price' => 'nullable|integer|gte:price',
            'stock' => 'nullable|integer|min:0',
            'has_variants' => 'nullable|boolean',
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.price' => 'nullable|integer|min:0',
            'badge' => 'nullable|in:baru,terlaris,promo',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'image_url' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        } else {
            $data['image'] = 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=900&q=80';
        }
        unset($data['image_url']);

        $hasVariants = $request->boolean('has_variants') && ! empty($request->input('variants'));
        $variantsInput = $hasVariants ? $request->input('variants', []) : [];

        if ($hasVariants && ! empty($variantsInput)) {
            $data['stock'] = (int) collect($variantsInput)->sum(fn ($v) => (int) ($v['stock'] ?? 0));
            $data['sizes'] = collect($variantsInput)->pluck('size')->filter(fn ($v) => filled($v))->unique()->values()->all() ?: null;
            $data['colors'] = collect($variantsInput)->pluck('color')->filter(fn ($v) => filled($v))->unique()->values()->all() ?: null;
        } else {
            $data['stock'] = (int) ($request->input('stock') ?? 0);
            $data['sizes'] = null;
            $data['colors'] = null;
        }

        unset($data['has_variants'], $data['variants']);

        $product = Product::create($data);

        if ($hasVariants && ! empty($variantsInput)) {
            foreach ($variantsInput as $v) {
                if (filled($v['size'] ?? null) || filled($v['color'] ?? null) || isset($v['stock'])) {
                    $product->variants()->create([
                        'size' => filled($v['size'] ?? null) ? trim($v['size']) : null,
                        'color' => filled($v['color'] ?? null) ? trim($v['color']) : null,
                        'stock' => (int) ($v['stock'] ?? 0),
                        'price' => ! empty($v['price']) ? (int) $v['price'] : null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('variants');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'specification' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'compare_price' => 'nullable|integer|gte:price',
            'stock' => 'nullable|integer|min:0',
            'has_variants' => 'nullable|boolean',
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.price' => 'nullable|integer|min:0',
            'badge' => 'nullable|in:baru,terlaris,promo',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'image_url' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        } else {
            unset($data['image']);
        }
        unset($data['image_url']);

        $hasVariants = $request->boolean('has_variants') && ! empty($request->input('variants'));
        $variantsInput = $hasVariants ? $request->input('variants', []) : [];

        if ($hasVariants && ! empty($variantsInput)) {
            $data['stock'] = (int) collect($variantsInput)->sum(fn ($v) => (int) ($v['stock'] ?? 0));
            $data['sizes'] = collect($variantsInput)->pluck('size')->filter(fn ($v) => filled($v))->unique()->values()->all() ?: null;
            $data['colors'] = collect($variantsInput)->pluck('color')->filter(fn ($v) => filled($v))->unique()->values()->all() ?: null;
        } else {
            $data['stock'] = (int) ($request->input('stock') ?? 0);
            $data['sizes'] = null;
            $data['colors'] = null;
        }

        unset($data['has_variants'], $data['variants']);

        $product->update($data);

        $product->variants()->delete();
        if ($hasVariants && ! empty($variantsInput)) {
            foreach ($variantsInput as $v) {
                if (filled($v['size'] ?? null) || filled($v['color'] ?? null) || isset($v['stock'])) {
                    $product->variants()->create([
                        'size' => filled($v['size'] ?? null) ? trim($v['size']) : null,
                        'color' => filled($v['color'] ?? null) ? trim($v['color']) : null,
                        'stock' => (int) ($v['stock'] ?? 0),
                        'price' => ! empty($v['price']) ? (int) $v['price'] : null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
