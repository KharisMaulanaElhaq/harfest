<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'quota',
        'people_per_ticket',
        'requires_student_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'quota' => 'integer',
            'people_per_ticket' => 'integer',
            'requires_student_id' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Calculate remaining quota by deducting pending, waiting, and paid orders.
     */
    public function getRemainingQuotaAttribute(): int
    {
        $used = $this->orders()
            ->whereIn('status', OrderStatus::countingStatuses())
            ->sum('quantity');

        return max(0, (int) $this->quota - (int) $used);
    }

    public function remainingQuota(): int
    {
        return $this->remaining_quota;
    }

    public function committedQuota(): int
    {
        return (int) $this->orders()
            ->whereIn('status', OrderStatus::countingStatuses())
            ->sum('quantity');
    }

    public function soldQuota(): int
    {
        return (int) $this->orders()
            ->where('status', OrderStatus::Paid)
            ->sum('quantity');
    }
}
