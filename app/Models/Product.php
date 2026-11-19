<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'specification',
        'price', 'compare_price', 'stock', 'rating', 'sold',
        'sizes', 'colors', 'badge', 'image', 'is_featured', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sizes' => 'array',
            'colors' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function hasVariants(): bool
    {
        return $this->relationLoaded('variants')
            ? $this->variants->isNotEmpty()
            : $this->variants()->exists();
    }

    public function findVariant(?string $size, ?string $color): ?ProductVariant
    {
        $variants = $this->relationLoaded('variants')
            ? $this->variants
            : $this->variants()->get();

        return $variants->first(function (ProductVariant $variant) use ($size, $color) {
            $sizeMatches = (! $variant->size && ! $size) || (strcasecmp((string) $variant->size, (string) $size) === 0);
            $colorMatches = (! $variant->color && ! $color) || (strcasecmp((string) $variant->color, (string) $color) === 0);

            return $sizeMatches && $colorMatches;
        });
    }

    public function syncVariantStock(): void
    {
        if ($this->hasVariants()) {
            $this->update(['stock' => (int) $this->variants()->sum('stock')]);
        }
    }

    public function updateRating(): void
    {
        $avg = $this->reviews()->avg('rating');
        $this->update([
            'rating' => $avg !== null ? round((float) $avg, 1) : 0,
        ]);
    }

    public function getSizesAttribute($value): ?array
    {
        $sizes = is_string($value) ? json_decode($value, true) : $value;

        if (empty($sizes) && $this->relationLoaded('variants')) {
            $sizes = $this->variants->pluck('size')->filter(fn ($v) => filled($v))->unique()->values()->all();
        }

        if (empty($sizes) || ! is_array($sizes)) {
            return null;
        }

        return self::sortSizes($sizes);
    }

    /**
     * Mengurutkan daftar ukuran dari yang terkecil ke yang terbesar.
     * Contoh: ['M', 'S', 'L', 'XS', 'XL', 'XXL'] -> ['XS', 'S', 'M', 'L', 'XL', 'XXL']
     * Angka sepatu: ['42', '39', '44', '40'] -> ['39', '40', '42', '44']
     */
    public static function sortSizes(array $sizes): array
    {
        $order = [
            'xxs' => 1,
            'xs' => 2,
            's' => 3,
            'm' => 4,
            'l' => 5,
            'xl' => 6,
            'xxl' => 7,
            '2xl' => 7,
            'xxxl' => 8,
            '3xl' => 8,
            '4xl' => 9,
            '5xl' => 10,
            'all size' => 99,
            'free size' => 99,
            'one size' => 99,
        ];

        usort($sizes, function ($a, $b) use ($order) {
            $aTrim = strtolower(trim((string) $a));
            $bTrim = strtolower(trim((string) $b));

            $aIsApparel = isset($order[$aTrim]);
            $bIsApparel = isset($order[$bTrim]);

            if ($aIsApparel && $bIsApparel) {
                return $order[$aTrim] <=> $order[$bTrim];
            }

            if ($aIsApparel) {
                return -1;
            }

            if ($bIsApparel) {
                return 1;
            }

            $aIsNumeric = is_numeric($aTrim);
            $bIsNumeric = is_numeric($bTrim);

            if ($aIsNumeric && $bIsNumeric) {
                return (float) $aTrim <=> (float) $bTrim;
            }

            return strnatcasecmp($aTrim, $bTrim);
        });

        return array_values($sizes);
    }

    public function getPriceLabelAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }

    public function getImageAttribute(?string $value): string
    {
        if (blank($value)) {
            return 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=900&q=80';
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
            return asset(ltrim($value, '/'));
        }

        return asset('storage/'.ltrim($value, '/'));
    }

    public function getDiscountPercentAttribute(): int
    {
        if (empty($this->compare_price) || $this->compare_price <= $this->price) {
            return 0;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function getIsDiscountedAttribute(): bool
    {
        return $this->discount_percent > 0;
    }

    public function isWishlisted(?int $userId = null): bool
    {
        $userId ??= auth()->id();

        if (! $userId) {
            return false;
        }

        return Wishlist::where('user_id', $userId)->where('product_id', $this->id)->exists();
    }
}
