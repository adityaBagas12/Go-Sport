<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'stock',
        'price',
        'sku',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'price' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getLabelAttribute(): string
    {
        $parts = [];
        if ($this->size) {
            $parts[] = 'Ukuran: '.$this->size;
        }
        if ($this->color) {
            $parts[] = 'Warna: '.$this->color;
        }

        return ! empty($parts) ? implode(', ', $parts) : 'Default';
    }

    public function getEffectivePriceAttribute(): int
    {
        return $this->price ?? $this->product->price;
    }
}
