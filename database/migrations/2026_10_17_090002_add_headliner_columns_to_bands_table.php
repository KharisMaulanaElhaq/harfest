<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bands', function (Blueprint $table) {
            $table->boolean('is_headliner')->default(false)->after('photo');
            $table->string('logo', 255)->nullable()->after('is_headliner');
            $table->string('instagram_url', 255)->nullable()->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bands', function (Blueprint $table) {
            $table->dropColumn(['is_headliner', 'logo', 'instagram_url']);
        });
    }
};
