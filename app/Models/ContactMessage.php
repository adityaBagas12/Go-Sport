<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'subject',
        'message',
        'is_read',
        'read_at',
        'admin_reply',
        'replied_at',
        'replied_by',
        'customer_read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'replied_at' => 'datetime',
            'customer_read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function repliedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function isReplied(): bool
    {
        return ! empty($this->admin_reply);
    }

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsReplied(string $reply, int $adminId): void
    {
        $this->update([
            'admin_reply' => $reply,
            'replied_at' => now(),
            'replied_by' => $adminId,
            'is_read' => true,
            'customer_read_at' => null,
        ]);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeReplied(Builder $query): Builder
    {
        return $query->whereNotNull('admin_reply');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('admin_reply');
    }
}
