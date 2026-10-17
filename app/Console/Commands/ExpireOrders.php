<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire pending payment orders that have passed their expiration deadline';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $query = Order::query()
            ->where('status', OrderStatus::PendingPayment)
            ->where('expires_at', '<', $now);

        $count = $query->count();

        if ($count === 0) {
            $this->info('Tidak ada pesanan yang perlu di-expire.');
            return self::SUCCESS;
        }

        // Update orders to expired
        $affected = Order::query()
            ->where('status', OrderStatus::PendingPayment)
            ->where('expires_at', '<', $now)
            ->update([
                'status' => OrderStatus::Expired,
                'updated_at' => $now,
            ]);

        $this->info("Berhasil mengubah {$affected} pesanan pending yang kedaluwarsa menjadi status 'expired'.");
        Log::info("Command app:expire-orders ran: {$affected} orders expired.");

        return self::SUCCESS;
    }
}
