<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Band extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'photo',
        'is_headliner',
        'logo',
        'instagram_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_headliner' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get photo URL or fallback.
     */
    public function photoUrl(): ?string
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return asset('storage/' . $this->photo);
        }

        return null;
    }

    /**
     * Get logo URL.
     */
    public function logoUrl(): ?string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }

        return null;
    }
}
