<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'venue',
        'event_date',
        'poster',
        'edition',
        'slug',
        'tagline',
        'logo',
        'banner',
        'status',
        'sales_open_at',
        'sales_close_at',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'sales_open_at' => 'datetime',
            'sales_close_at' => 'datetime',
            'edition' => 'integer',
            'status' => EventStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->edition)) {
                $event->edition = (static::max('edition') ?? 0) + 1;
            }
            if (empty($event->slug)) {
                $event->slug = 'vol-' . $event->edition;
            }
            if (empty($event->status)) {
                $event->status = EventStatus::Draft;
            }
        });
    }

    public function bands(): HasMany
    {
        return $this->hasMany(Band::class)->orderBy('sort_order');
    }

    public function headliners(): HasMany
    {
        return $this->hasMany(Band::class)->where('is_headliner', true)->orderBy('sort_order');
    }

    public function regularBands(): HasMany
    {
        return $this->hasMany(Band::class)->where('is_headliner', false)->orderBy('sort_order');
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if ticket sales are currently open for this event.
     */
    public function isSalesOpen(): bool
    {
        if ($this->status !== EventStatus::OnSale) {
            return false;
        }

        $now = now();

        if ($this->sales_open_at && $now->lt($this->sales_open_at)) {
            return false;
        }

        if ($this->sales_close_at && $now->gt($this->sales_close_at)) {
            return false;
        }

        if ($this->event_date && $now->gt($this->event_date)) {
            return false;
        }

        return true;
    }

    /**
     * Scope query to find active event with priority: on_sale > upcoming > finished.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE 
                WHEN status = 'on_sale' THEN 1
                WHEN status = 'upcoming' THEN 2
                WHEN status = 'finished' THEN 3
                ELSE 4
            END ASC
        ")->orderByRaw("
            CASE 
                WHEN status = 'finished' THEN UNIX_TIMESTAMP(event_date) * -1
                ELSE UNIX_TIMESTAMP(event_date)
            END ASC
        ");
    }

    /**
     * Return custom event logo or fall back to official transparent logo.
     */
    public function logoUrl(): string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }

        return asset('images/brand/harfest-logo.png');
    }

    /**
     * Return custom banner image if uploaded.
     */
    public function bannerUrl(): ?string
    {
        if ($this->banner && Storage::disk('public')->exists($this->banner)) {
            return asset('storage/' . $this->banner);
        }

        return null;
    }
}
