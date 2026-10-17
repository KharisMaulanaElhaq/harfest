<?php

namespace App\Http\Middleware;

use App\Models\Event;
use App\Services\EventService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetAdminEvent
{
    public function __construct(
        protected EventService $eventService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminEventId = session('admin_event_id');
        $event = null;

        if ($adminEventId) {
            $event = Event::find($adminEventId);
        }

        if (! $event) {
            $event = $this->eventService->active() ?? Event::orderBy('edition', 'desc')->first();
            if ($event) {
                session(['admin_event_id' => $event->id]);
            }
        }

        $allEvents = Event::orderBy('edition', 'desc')->get();

        // Share globally with admin views
        View::share('currentAdminEvent', $event);
        View::share('allAdminEvents', $allEvents);

        // Store in request for controllers to access
        $request->attributes->set('admin_event', $event);

        return $next($request);
    }
}
