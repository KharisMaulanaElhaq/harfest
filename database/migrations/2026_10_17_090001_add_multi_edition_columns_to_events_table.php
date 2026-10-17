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
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedSmallInteger('edition')->nullable()->after('event_date');
            $table->string('slug', 50)->nullable()->after('edition');
            $table->string('tagline', 150)->nullable()->after('slug');
            $table->string('logo', 255)->nullable()->after('poster');
            $table->string('banner', 255)->nullable()->after('logo');
            $table->string('status', 20)->default('upcoming')->after('banner');
            $table->dateTime('sales_open_at')->nullable()->after('status');
            $table->dateTime('sales_close_at')->nullable()->after('sales_open_at');
        });

        // Backfill existing event as Vol 1
        $events = DB::table('events')->get();
        foreach ($events as $index => $event) {
            $ed = $index + 1;
            DB::table('events')->where('id', $event->id)->update([
                'edition' => $ed,
                'slug' => 'vol-' . $ed,
                'status' => 'finished',
                'tagline' => 'Harkat Negeri Fest',
            ]);
        }

        Schema::table('events', function (Blueprint $table) {
            $table->unique('edition');
            $table->unique('slug');
            if (Schema::hasColumn('events', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->dropUnique(['edition']);
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'edition',
                'slug',
                'tagline',
                'logo',
                'banner',
                'status',
                'sales_open_at',
                'sales_close_at',
            ]);
        });
    }
};
