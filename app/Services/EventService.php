<?php

namespace App\Services;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventService
{
    /**
     * Get the active event with priority: on_sale (closest date) > upcoming > finished (newest).
     *
     * We cache only the event ID (an integer) to avoid __PHP_Incomplete_Class errors that occur
     * when a serialized Eloquent model sitting in the cache cannot be unserialized after class
     * changes or autoloader reloads.
     */
    public function active(): ?Event
    {
        $id = Cache::remember('harfest_active_event', 60, function () {
            // 1. First look for on_sale event with closest future or current event_date
            $event = Event::where('status', EventStatus::OnSale)
                ->orderBy('event_date', 'asc')
                ->value('id');

            if ($event !== null) {
                return $event;
            }

            // 2. Next look for upcoming event with closest event_date
            $event = Event::where('status', EventStatus::Upcoming)
                ->orderBy('event_date', 'asc')
                ->value('id');

            if ($event !== null) {
                return $event;
            }

            // 3. Fallback to finished event with newest event_date
            return Event::where('status', EventStatus::Finished)
                ->orderBy('event_date', 'desc')
                ->value('id');
        });

        return $id !== null ? Event::find($id) : null;
    }

    /**
     * Clear cached active event.
     */
    public function clearCache(): void
    {
        Cache::forget('harfest_active_event');
    }

    /**
     * Duplicate an existing event: copies event details and ticket types.
     * Does NOT copy orders, tickets, or bands.
     */
    public function duplicate(Event $source): Event
    {
        return DB::transaction(function () use ($source) {
            $maxEdition = (int) (Event::max('edition') ?? 1);
            $newEdition = $maxEdition + 1;
            $newSlug = 'vol-' . $newEdition;

            // Ensure unique slug
            $counter = 1;
            while (Event::where('slug', $newSlug)->exists()) {
                $counter++;
                $newSlug = "vol-{$newEdition}-{$counter}";
            }

            $newEvent = Event::create([
                'name' => "Harfest Vol {$newEdition}",
                'edition' => $newEdition,
                'slug' => $newSlug,
                'tagline' => $source->tagline,
                'description' => $source->description,
                'venue' => $source->venue,
                'event_date' => $source->event_date ? $source->event_date->copy()->addYear() : now()->addYear(),
                'poster' => null,
                'logo' => null,
                'banner' => null,
                'status' => EventStatus::Draft,
                'sales_open_at' => null,
                'sales_close_at' => null,
            ]);

            // Duplicate ticket types
            foreach ($source->ticketTypes as $ticketType) {
                $newEvent->ticketTypes()->create([
                    'name' => $ticketType->name,
                    'description' => $ticketType->description,
                    'price' => $ticketType->price,
                    'quota' => $ticketType->quota,
                    'people_per_ticket' => $ticketType->people_per_ticket,
                    'requires_student_id' => $ticketType->requires_student_id,
                    'is_active' => true,
                ]);
            }

            $this->clearCache();

            return $newEvent;
        });
    }

    /**
     * Update event status with transition validation.
     */
    public function updateStatus(Event $event, EventStatus $to, bool $force = false): Event
    {
        if (! $force) {
            $allowedTransitions = [
                EventStatus::Draft->value => [EventStatus::Upcoming, EventStatus::OnSale],
                EventStatus::Upcoming->value => [EventStatus::OnSale, EventStatus::Draft],
                EventStatus::OnSale->value => [EventStatus::Finished, EventStatus::Upcoming],
                EventStatus::Finished->value => [],
            ];

            $currentVal = $event->status instanceof EventStatus ? $event->status->value : $event->status;
            $allowed = $allowedTransitions[$currentVal] ?? [];

            if (! in_array($to, $allowed, true) && $event->status !== $to) {
                throw ValidationException::withMessages([
                    'status' => "Transisi status dari {$event->status->label()} ke {$to->label()} tidak diizinkan tanpa konfirmasi admin.",
                ]);
            }
        }

        $event->update(['status' => $to]);
        $this->clearCache();

        return $event;
    }
}
