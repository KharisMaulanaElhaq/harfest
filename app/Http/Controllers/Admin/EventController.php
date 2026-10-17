<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        protected EventService $eventService
    ) {}

    /**
     * Display a listing of events with duplicate and status change actions.
     */
    public function index(): View
    {
        $events = Event::withCount(['bands', 'ticketTypes', 'orders'])
            ->orderBy('edition', 'desc')
            ->get();

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        $nextEdition = (Event::max('edition') ?? 0) + 1;
        $defaultSlug = 'vol-' . $nextEdition;

        return view('admin.events.create', compact('nextEdition', 'defaultSlug'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $request->filled('edition')) {
            $nextEd = (Event::max('edition') ?? 0) + 1;
            $request->merge(['edition' => $nextEd]);
        }
        if (! $request->filled('slug')) {
            $request->merge(['slug' => 'vol-' . $request->input('edition')]);
        }
        if (! $request->filled('status')) {
            $request->merge(['status' => EventStatus::OnSale->value]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'edition' => ['required', 'integer', 'min:1', 'unique:events,edition'],
            'slug' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:events,slug'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'venue' => ['required', 'string', 'max:150'],
            'event_date' => ['required', 'date'],
            'status' => ['required', Rule::enum(EventStatus::class)],
            'sales_open_at' => ['nullable', 'date'],
            'sales_close_at' => ['nullable', 'date', 'after_or_equal:sales_open_at'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('events/posters', 'public');
        }

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('events/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('events/banners', 'public');
        }

        $event = Event::create($validated);
        $this->eventService->clearCache();

        return redirect()->route('admin.events.index')->with('success', "Event [{$event->name}] berhasil dibuat!");
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        if (! $request->filled('edition')) {
            $request->merge(['edition' => $event->edition]);
        }
        if (! $request->filled('slug')) {
            $request->merge(['slug' => $event->slug]);
        }
        if (! $request->filled('status')) {
            $request->merge(['status' => $event->status->value]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'edition' => ['required', 'integer', 'min:1', Rule::unique('events', 'edition')->ignore($event->id)],
            'slug' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('events', 'slug')->ignore($event->id)],
            'tagline' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'venue' => ['required', 'string', 'max:150'],
            'event_date' => ['required', 'date'],
            'status' => ['required', Rule::enum(EventStatus::class)],
            'sales_open_at' => ['nullable', 'date'],
            'sales_close_at' => ['nullable', 'date'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        if ($request->hasFile('poster')) {
            if ($event->poster && Storage::disk('public')->exists($event->poster)) {
                Storage::disk('public')->delete($event->poster);
            }
            $validated['poster'] = $request->file('poster')->store('events/posters', 'public');
        }

        if ($request->hasFile('logo')) {
            if ($event->logo && Storage::disk('public')->exists($event->logo)) {
                Storage::disk('public')->delete($event->logo);
            }
            $validated['logo'] = $request->file('logo')->store('events/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($event->banner && Storage::disk('public')->exists($event->banner)) {
                Storage::disk('public')->delete($event->banner);
            }
            $validated['banner'] = $request->file('banner')->store('events/banners', 'public');
        }

        $event->update($validated);
        $this->eventService->clearCache();

        return redirect()->route('admin.events.index')->with('success', 'Data event berhasil diperbarui!');
    }

    /**
     * Switch active working event in session.
     */
    public function switch(Event $event): RedirectResponse
    {
        session(['admin_event_id' => $event->id]);

        return back()->with('success', "Event kerja admin berhasil dialihkan ke {$event->name}.");
    }

    /**
     * Duplicate event and its ticket types (does not copy orders, tickets, or bands).
     */
    public function duplicate(Event $event): RedirectResponse
    {
        $newEvent = $this->eventService->duplicate($event);

        return redirect()
            ->route('admin.events.index')
            ->with('success', "Event {$event->name} berhasil diduplikasi menjadi {$newEvent->name} (Draft)! Silakan sesuaikan tanggal dan harga tiket.");
    }

    /**
     * Quickly update event status from list or switcher.
     */
    public function updateStatus(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(EventStatus::class)],
        ]);

        $this->eventService->updateStatus($event, EventStatus::from($validated['status']), true);

        return back()->with('success', "Status event {$event->name} berhasil diubah menjadi {$event->status->label()}.");
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        if ($event->orders()->exists()) {
            return back()->with('error', 'Event tidak dapat dihapus karena sudah memiliki riwayat pesanan.');
        }

        if ($event->poster && Storage::disk('public')->exists($event->poster)) {
            Storage::disk('public')->delete($event->poster);
        }

        if ($event->logo && Storage::disk('public')->exists($event->logo)) {
            Storage::disk('public')->delete($event->logo);
        }

        if ($event->banner && Storage::disk('public')->exists($event->banner)) {
            Storage::disk('public')->delete($event->banner);
        }

        $event->delete();
        $this->eventService->clearCache();

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }
}
