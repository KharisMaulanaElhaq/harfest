<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->nullable()->after('booking_code');
        });

        // Backfill event_id from ticket_types
        DB::statement('UPDATE orders SET event_id = (SELECT event_id FROM ticket_types WHERE ticket_types.id = orders.ticket_type_id) WHERE orders.event_id IS NULL');

        $firstEventId = DB::table('events')->value('id');
        if ($firstEventId) {
            DB::table('orders')->whereNull('event_id')->update(['event_id' => $firstEventId]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->nullable(false)->change();
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->index(['event_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropIndex(['event_id', 'status']);
            $table->dropColumn('event_id');
        });
    }
};
