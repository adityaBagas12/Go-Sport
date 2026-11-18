<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Validasi: hanya pesanan yang sudah dibayar/diproses/dikirim/selesai yang bisa diulas
        if ($order->payment_status !== 'paid' && ! in_array($order->status, ['diproses', 'dikirim', 'selesai'], true)) {
            return back()->with('error', 'Ulasan hanya dapat diberikan setelah produk dibayar/diterima.');
        }

        // Validasi: produk harus ada di pesanan tersebut
        $hasProduct = $order->items()->where('product_id', $request->product_id)->exists();
        if (! $hasProduct) {
            return back()->with('error', 'Produk ini tidak ditemukan dalam rincian pesanan Anda.');
        }

        $product = Product::findOrFail($request->product_id);

        Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'order_id' => $order->id,
            ],
            [
                'rating' => (int) $request->rating,
                'comment' => $request->comment,
            ]
        );

        $product->updateRating();

        return back()->with('success', "Terima kasih! Penilaian bintang {$request->rating} untuk \"{$product->name}\" berhasil disimpan.");
    }
}
