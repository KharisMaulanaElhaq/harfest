<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Shifts historical timestamp columns by +7 hours to align with Asia/Jakarta (WIB).
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('UPDATE orders SET created_at = DATE_ADD(created_at, INTERVAL 7 HOUR), updated_at = DATE_ADD(updated_at, INTERVAL 7 HOUR), expires_at = DATE_ADD(expires_at, INTERVAL 7 HOUR)');
            DB::statement('UPDATE payments SET created_at = DATE_ADD(created_at, INTERVAL 7 HOUR), updated_at = DATE_ADD(updated_at, INTERVAL 7 HOUR), verified_at = CASE WHEN verified_at IS NOT NULL THEN DATE_ADD(verified_at, INTERVAL 7 HOUR) ELSE NULL END');
            DB::statement('UPDATE tickets SET created_at = DATE_ADD(created_at, INTERVAL 7 HOUR), updated_at = DATE_ADD(updated_at, INTERVAL 7 HOUR), used_at = CASE WHEN used_at IS NOT NULL THEN DATE_ADD(used_at, INTERVAL 7 HOUR) ELSE NULL END');
        } elseif ($driver === 'sqlite') {
            DB::statement("UPDATE orders SET created_at = datetime(created_at, '+7 hours'), updated_at = datetime(updated_at, '+7 hours'), expires_at = datetime(expires_at, '+7 hours')");
            DB::statement("UPDATE payments SET created_at = datetime(created_at, '+7 hours'), updated_at = datetime(updated_at, '+7 hours'), verified_at = CASE WHEN verified_at IS NOT NULL THEN datetime(verified_at, '+7 hours') ELSE NULL END");
            DB::statement("UPDATE tickets SET created_at = datetime(created_at, '+7 hours'), updated_at = datetime(updated_at, '+7 hours'), used_at = CASE WHEN used_at IS NOT NULL THEN datetime(used_at, '+7 hours') ELSE NULL END");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('UPDATE orders SET created_at = DATE_SUB(created_at, INTERVAL 7 HOUR), updated_at = DATE_SUB(updated_at, INTERVAL 7 HOUR), expires_at = DATE_SUB(expires_at, INTERVAL 7 HOUR)');
            DB::statement('UPDATE payments SET created_at = DATE_SUB(created_at, INTERVAL 7 HOUR), updated_at = DATE_SUB(updated_at, INTERVAL 7 HOUR), verified_at = CASE WHEN verified_at IS NOT NULL THEN DATE_SUB(verified_at, INTERVAL 7 HOUR) ELSE NULL END');
            DB::statement('UPDATE tickets SET created_at = DATE_SUB(created_at, INTERVAL 7 HOUR), updated_at = DATE_SUB(updated_at, INTERVAL 7 HOUR), used_at = CASE WHEN used_at IS NOT NULL THEN DATE_SUB(used_at, INTERVAL 7 HOUR) ELSE NULL END');
        } elseif ($driver === 'sqlite') {
            DB::statement("UPDATE orders SET created_at = datetime(created_at, '-7 hours'), updated_at = datetime(updated_at, '-7 hours'), expires_at = datetime(expires_at, '-7 hours')");
            DB::statement("UPDATE payments SET created_at = datetime(created_at, '-7 hours'), updated_at = datetime(updated_at, '-7 hours'), verified_at = CASE WHEN verified_at IS NOT NULL THEN datetime(verified_at, '-7 hours') ELSE NULL END");
            DB::statement("UPDATE tickets SET created_at = datetime(created_at, '-7 hours'), updated_at = datetime(updated_at, '-7 hours'), used_at = CASE WHEN used_at IS NOT NULL THEN datetime(used_at, '-7 hours') ELSE NULL END");
        }
    }
};

