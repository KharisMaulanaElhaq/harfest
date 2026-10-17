<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Display the Harfest landing page with active event.
     */
    public function index(EventService $eventService): View
    {
        $event = $eventService->active();

        if ($event) {
            $event->load([
                'bands' => fn ($query) => $query->orderBy('sort_order'),
                'ticketTypes' => fn ($query) => $query->where('is_active', true)->orderBy('price'),
            ]);
        }

        $bands = $event ? $event->bands : collect();
        $headliners = $event ? $event->headliners : collect();
        $regularBands = $event ? $event->regularBands : collect();
        $ticketTypes = $event ? $event->ticketTypes : collect();

        return view('landing.index', compact('event', 'bands', 'headliners', 'regularBands', 'ticketTypes'));
    }
}
