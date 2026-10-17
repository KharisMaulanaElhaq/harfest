<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketTypeController extends Controller
{
    /**
     * Display a listing of ticket types.
     */
    public function index(Request $request): View
    {
        $currentEvent = $request->attributes->get('admin_event');
        $allEvents = Event::orderBy('edition', 'desc')->get();

        $query = TicketType::with('event')->orderBy('price');

        if ($request->has('event_id') && $request->input('event_id') !== 'all') {
            $query->where('event_id', $request->input('event_id'));
        } elseif ($currentEvent) {
            $query->where('event_id', $currentEvent->id);
        }

        $ticketTypes = $query->get();

        return view('admin.ticket-types.index', compact('ticketTypes', 'allEvents', 'currentEvent'));
    }

    /**
     * Show the form for creating a new ticket type.
     */
    public function create(Request $request): View
    {
        $currentEvent = $request->attributes->get('admin_event');
        $events = Event::orderBy('edition', 'desc')->get();

        return view('admin.ticket-types.create', compact('events', 'currentEvent'));
    }

    /**
     * Store a newly created ticket type in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'quota' => ['required', 'integer', 'min:1'],
            'people_per_ticket' => ['required', 'integer', 'min:1', 'max:10'],
            'requires_student_id' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['requires_student_id'] = $request->boolean('requires_student_id');
        $validated['is_active'] = $request->boolean('is_active', true);

        TicketType::create($validated);

        return redirect()->route('admin.ticket-types.index')->with('success', 'Kategori tiket baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified ticket type.
     */
    public function edit(TicketType $ticketType): View
    {
        $events = Event::orderBy('edition', 'desc')->get();
        $committedQuota = $ticketType->committedQuota();

        return view('admin.ticket-types.edit', compact('ticketType', 'events', 'committedQuota'));
    }

    /**
     * Update the specified ticket type in storage.
     * Rule: Kuota tidak boleh di bawah jumlah terjual / committed.
     */
    public function update(Request $request, TicketType $ticketType): RedirectResponse
    {
        $minQuota = $ticketType->committedQuota();

        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'quota' => ['required', 'integer', 'min:' . $minQuota],
            'people_per_ticket' => ['required', 'integer', 'min:1', 'max:10'],
            'requires_student_id' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'quota.min' => "Kuota tiket tidak boleh di bawah jumlah yang telah terjual / dipesan ({$minQuota} paket).",
        ]);

        $validated['requires_student_id'] = $request->boolean('requires_student_id');
        $validated['is_active'] = $request->boolean('is_active');

        $ticketType->update($validated);

        return redirect()->route('admin.ticket-types.index')->with('success', 'Kategori tiket berhasil diperbarui!');
    }

    /**
     * Remove the specified ticket type from storage.
     */
    public function destroy(TicketType $ticketType): RedirectResponse
    {
        if ($ticketType->orders()->exists()) {
            return back()->with('error', 'Kategori tiket tidak dapat dihapus karena sudah memiliki riwayat pesanan.');
        }

        $ticketType->delete();

        return redirect()->route('admin.ticket-types.index')->with('success', 'Kategori tiket berhasil dihapus.');
    }
}
