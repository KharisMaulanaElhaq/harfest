<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
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
     * Scope to preload quota stats in a single query per collection.
     *
     * Adds two aggregated columns to each TicketType instance:
     *  - committed_qty : sum of quantity for pending + waiting + paid orders
     *  - sold_qty      : sum of quantity for paid orders only
     *
     * Usage:
     *   TicketType::withQuotaStats()->get()
     *   $event->ticketTypes()->withQuotaStats()->get()
     *
     * After loading, access stats via:
     *   $type->remaining_quota  (uses committed_qty if preloaded)
     *   $type->committed_qty
     *   $type->sold_qty
     */
    public function scopeWithQuotaStats(Builder $query): Builder
    {
        $countingStatuses = OrderStatus::countingStatuses();

        return $query
            ->withSum(
                ['orders as committed_qty' => fn (Builder $q) => $q->whereIn('status', $countingStatuses)],
                'quantity'
            )
            ->withSum(
                ['orders as sold_qty' => fn (Builder $q) => $q->where('status', OrderStatus::Paid)],
                'quantity'
            );
    }

    /**
     * Calculate remaining quota.
     *
     * When withQuotaStats() scope was applied, uses the preloaded `committed_qty`
     * attribute to avoid an extra query. Falls back to a live DB query otherwise.
     */
    public function getRemainingQuotaAttribute(): int
    {
        $used = isset($this->attributes['committed_qty'])
            ? (int) $this->attributes['committed_qty']
            : (int) $this->orders()
                ->whereIn('status', OrderStatus::countingStatuses())
                ->sum('quantity');

        return max(0, $this->quota - $used);
    }

    /**
     * @deprecated Use $type->committed_qty (after withQuotaStats()) or remaining_quota directly.
     */
    public function remainingQuota(): int
    {
        return $this->remaining_quota;
    }

    /**
     * Quota committed by pending + waiting + paid orders.
     * Prefer withQuotaStats() scope to avoid N+1.
     */
    public function committedQuota(): int
    {
        return isset($this->attributes['committed_qty'])
            ? (int) $this->attributes['committed_qty']
            : (int) $this->orders()
                ->whereIn('status', OrderStatus::countingStatuses())
                ->sum('quantity');
    }

    /**
     * Quota sold (paid orders only).
     * Prefer withQuotaStats() scope to avoid N+1.
     */
    public function soldQuota(): int
    {
        return isset($this->attributes['sold_qty'])
            ? (int) $this->attributes['sold_qty']
            : (int) $this->orders()
                ->where('status', OrderStatus::Paid)
                ->sum('quantity');
    }
}
