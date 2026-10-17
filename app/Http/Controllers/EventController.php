<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of all events: active/upcoming on top, finished/archived below.
     */
    public function index(): View
    {
        $activeEvents = Event::whereIn('status', [EventStatus::OnSale, EventStatus::Upcoming])
            ->orderBy('event_date', 'asc')
            ->get();

        $finishedEvents = Event::where('status', EventStatus::Finished)
            ->orderBy('event_date', 'desc')
            ->get();

        return view('events.index', compact('activeEvents', 'finishedEvents'));
    }

    /**
     * Display the specified event details (poster, headliner, lineup, ticket types, badge).
     */
    public function show(Event $event): View
    {
        $event->load([
            'bands' => fn ($q) => $q->orderBy('sort_order'),
            'ticketTypes' => fn ($q) => $q->where('is_active', true)->orderBy('price'),
        ]);

        $headliners = $event->headliners;
        $regularBands = $event->regularBands;
        $ticketTypes = $event->ticketTypes;
        $isSalesOpen = $event->isSalesOpen();

        return view('events.show', compact('event', 'headliners', 'regularBands', 'ticketTypes', 'isSalesOpen'));
    }
}
