<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Band;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BandController extends Controller
{
    /**
     * Display a listing of bands.
     */
    public function index(Request $request): View
    {
        $currentEvent = $request->attributes->get('admin_event');
        $allEvents = Event::orderBy('edition', 'desc')->get();

        $query = Band::with('event')->orderBy('sort_order');

        if ($request->has('event_id') && $request->input('event_id') !== 'all') {
            $query->where('event_id', $request->input('event_id'));
        } elseif ($currentEvent) {
            $query->where('event_id', $currentEvent->id);
        }

        $bands = $query->get();

        return view('admin.bands.index', compact('bands', 'allEvents', 'currentEvent'));
    }

    /**
     * Show the form for creating a new band.
     */
    public function create(Request $request): View
    {
        $currentEvent = $request->attributes->get('admin_event');
        $events = Event::orderBy('edition', 'desc')->get();
        $nextOrder = (Band::when($currentEvent, fn ($q) => $q->where('event_id', $currentEvent->id))->max('sort_order') ?? 0) + 1;

        return view('admin.bands.create', compact('events', 'currentEvent', 'nextOrder'));
    }

    /**
     * Store a newly created band in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'],
            'is_headliner' => ['nullable', 'boolean'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('bands', 'public');
        }

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('bands/logos', 'public');
        }

        $validated['is_headliner'] = $request->boolean('is_headliner');

        Band::create($validated);

        return redirect()->route('admin.bands.index')->with('success', 'Band baru berhasil ditambahkan ke lineup!');
    }

    /**
     * Show the form for editing the specified band.
     */
    public function edit(Band $band): View
    {
        $events = Event::orderBy('edition', 'desc')->get();

        return view('admin.bands.edit', compact('band', 'events'));
    }

    /**
     * Update the specified band in storage.
     */
    public function update(Request $request, Band $band): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'],
            'is_headliner' => ['nullable', 'boolean'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('photo')) {
            if ($band->photo && Storage::disk('public')->exists($band->photo)) {
                Storage::disk('public')->delete($band->photo);
            }
            $validated['photo'] = $request->file('photo')->store('bands', 'public');
        }

        if ($request->hasFile('logo')) {
            if ($band->logo && Storage::disk('public')->exists($band->logo)) {
                Storage::disk('public')->delete($band->logo);
            }
            $validated['logo'] = $request->file('logo')->store('bands/logos', 'public');
        }

        $validated['is_headliner'] = $request->boolean('is_headliner');

        $band->update($validated);

        return redirect()->route('admin.bands.index')->with('success', 'Data band berhasil diperbarui!');
    }

    /**
     * Remove the specified band from storage.
     */
    public function destroy(Band $band): RedirectResponse
    {
        if ($band->photo && Storage::disk('public')->exists($band->photo)) {
            Storage::disk('public')->delete($band->photo);
        }

        if ($band->logo && Storage::disk('public')->exists($band->logo)) {
            Storage::disk('public')->delete($band->logo);
        }

        $band->delete();

        return redirect()->route('admin.bands.index')->with('success', 'Band berhasil dihapus dari lineup.');
    }
}
