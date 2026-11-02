<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FileController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;

// Publik
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [\App\Http\Controllers\EventController::class, 'show'])->name('events.show');

// User (auth)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('orders.index');
    })->name('dashboard');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create/{ticketType}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order:booking_code}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order:booking_code}/payment', [\App\Http\Controllers\PaymentProofController::class, 'store'])->name('orders.payment');
    Route::post('/orders/{order:booking_code}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/files/student-card/{order:booking_code}', [FileController::class, 'studentCard'])->name('files.student-card');
    Route::get('/files/payment/{payment}', [FileController::class, 'payment'])->name('files.payment');
    Route::get('/orders/{order:booking_code}/tickets', [TicketController::class, 'showOrderTickets'])->name('orders.tickets');
    Route::get('/orders/{order:booking_code}/tickets/pdf', [TicketController::class, 'downloadOrderTicketsPdf'])->name('orders.tickets.pdf');
    Route::get('/orders/{order:booking_code}/tickets/download', [TicketController::class, 'downloadOrderTicketsPdf'])->name('orders.tickets.download');
    Route::get('/orders/{order:booking_code}/tickets/{ticket:ticket_code}/pdf', [TicketController::class, 'downloadSingleTicketPdf'])->name('orders.tickets.single.pdf');
});

// Admin & Staff Panel (auth + role.admin + admin.event)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role.admin', 'admin.event'])->group(function () {
    // Shared: Dapat diakses oleh Admin dan Staff 
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/events/{event}/switch', [\App\Http\Controllers\Admin\EventController::class, 'switch'])->name('events.switch');

    Route::get('/orders', [\App\Http\Controllers\Admin\AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export', [\App\Http\Controllers\Admin\AdminOrderController::class, 'export'])->name('orders.export');

    Route::get('/checkin', [\App\Http\Controllers\Admin\CheckinController::class, 'index'])->name('checkin.index');
    Route::post('/checkin', [\App\Http\Controllers\Admin\CheckinController::class, 'verify'])->middleware('throttle:60,1');
    Route::post('/checkin/verify', [\App\Http\Controllers\Admin\CheckinController::class, 'verify'])->name('checkin.verify')->middleware('throttle:60,1');

    // Admin Only: Khusus Administrator (Akses Ditolak 403 untuk Staff)
    Route::middleware('can:is-admin')->group(function () {
        Route::get('/event', fn () => redirect()->route('admin.events.index'))->name('event.index');
        Route::post('/events/{event}/duplicate', [\App\Http\Controllers\Admin\EventController::class, 'duplicate'])->name('events.duplicate');
        Route::post('/events/{event}/status', [\App\Http\Controllers\Admin\EventController::class, 'updateStatus'])->name('events.status');
        Route::patch('/events/{event}/status', [\App\Http\Controllers\Admin\EventController::class, 'updateStatus'])->name('events.update-status');
        Route::resource('events', \App\Http\Controllers\Admin\EventController::class);

        Route::resource('bands', \App\Http\Controllers\Admin\BandController::class);
        Route::resource('ticket-types', \App\Http\Controllers\Admin\TicketTypeController::class);

        Route::get('/payments', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/approve', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'reject'])->name('payments.reject');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
