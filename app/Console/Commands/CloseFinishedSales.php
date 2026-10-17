<?php

namespace App\Console\Commands;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Console\Command;

class CloseFinishedSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-finished-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close ticket sales and mark events as finished if sales_close_at or event_date has passed';

    /**
     * Execute the console command.
     */
    public function handle(EventService $eventService): int
    {
        $now = now();

        $eventsToClose = Event::where('status', EventStatus::OnSale)
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNotNull('sales_close_at')
                      ->where('sales_close_at', '<', $now);
                })->orWhere(function ($q) use ($now) {
                    $q->whereNotNull('event_date')
                      ->where('event_date', '<', $now);
                });
            })
            ->get();

        $count = $eventsToClose->count();

        foreach ($eventsToClose as $event) {
            $eventService->updateStatus($event, EventStatus::Finished, true);
            $this->info("Event [{$event->name}] (#{$event->id}) has been updated to Finished.");
        }

        $this->info("Total {$count} finished event(s) sales closed.");

        return Command::SUCCESS;
    }
}
