<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getLabelAttribute(): string
    {
        return $this->type === 'percent'
            ? "{$this->value}%"
            : 'Rp '.number_format($this->value, 0, ',', '.');
    }

    public function calculateDiscount(int $subtotal): int
    {
        if ($subtotal < $this->min_order || ! $this->is_active) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return min($this->value, $subtotal);
        }

        return (int) ($subtotal * $this->value / 100);
    }
}
