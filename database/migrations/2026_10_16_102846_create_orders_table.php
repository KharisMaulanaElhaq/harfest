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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->restrictOnDelete();
            $table->unsignedSmallInteger('quantity');
            $table->unsignedInteger('total_price');
            $table->string('student_id', 30)->nullable();
            $table->string('student_card_path', 255)->nullable();
            $table->enum('status', [
                'pending_payment',
                'waiting_verification',
                'paid',
                'rejected',
                'expired',
                'cancelled',
            ])->default('pending_payment');
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index(['ticket_type_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
