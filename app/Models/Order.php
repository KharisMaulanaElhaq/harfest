<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'event_id',
        'user_id',
        'ticket_type_id',
        'quantity',
        'total_price',
        'student_id',
        'student_card_path',
        'status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'total_price' => 'integer',
            'status' => OrderStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->event_id) && ! empty($order->ticket_type_id)) {
                $order->event_id = TicketType::whereKey($order->ticket_type_id)->value('event_id');
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function isExpired(): bool
    {
        return $this->status === OrderStatus::Expired || ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Scope query to specific event.
     *
     * @param  \App\Models\Event|int  $event
     */
    public function scopeForEvent(Builder $query, Event|int $event): Builder
    {
        $eventId = $event instanceof Event ? $event->id : $event;

        return $query->where('orders.event_id', $eventId);
    }
}
