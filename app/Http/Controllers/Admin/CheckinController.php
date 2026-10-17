<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\CheckinService;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckinController extends Controller
{
    public function __construct(
        protected CheckinService $checkinService,
        protected EventService $eventService
    ) {}

    /**
     * Display the check-in scanner and manual entry page with event selector.
     */
    public function index(Request $request): View
    {
        $allEvents = Event::orderBy('edition', 'desc')->get();
        $selectedEvent = $request->attributes->get('admin_event')
            ?? $this->eventService->active()
            ?? $allEvents->first();

        return view('admin.checkin.index', compact('allEvents', 'selectedEvent'));
    }

    /**
     * Verify ticket code and perform check-in for the selected event.
     */
    public function verify(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'ticket_code' => ['required', 'string', 'max:50'],
            'event_id' => ['nullable', 'exists:events,id'],
        ], [
            'ticket_code.required' => 'Kode tiket wajib diisi atau di-scan.',
        ]);

        $code = $request->input('ticket_code');
        $eventId = $request->input('event_id') ?? session('admin_event_id');
        $event = $eventId ? Event::find($eventId) : ($request->attributes->get('admin_event') ?? $this->eventService->active());

        $result = $this->checkinService->verify($code, Auth::user(), $event);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return redirect()
            ->route('admin.checkin.index')
            ->with('checkin_result', $result);
    }
}
