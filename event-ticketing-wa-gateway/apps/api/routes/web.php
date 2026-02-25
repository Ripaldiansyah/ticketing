<?php

use App\Livewire\Admin\EventList;
use App\Livewire\Admin\OrderCreate;
use App\Livewire\Admin\OrderList;
use App\Livewire\Admin\TicketList;
use App\Livewire\Staff\CheckIn;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('dashboard', App\Livewire\Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Admin Routes
// Admin Only Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/events', EventList::class)->name('events');
    Route::get('/orders', OrderList::class)->name('orders');

    Route::get('/whatsapp', App\Livewire\Admin\WhatsAppSettings::class)->name('whatsapp');
    Route::get('/templates', App\Livewire\Admin\WaTemplateList::class)->name('templates');
});

// Staff & Admin Shared Routes
Route::middleware(['auth', 'role:staff,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/tickets', TicketList::class)->name('tickets');
});

// Check-in Route
Route::middleware(['auth', 'role:staff,admin'])->group(function () {
    Route::get('/checkin', CheckIn::class)->name('checkin');
});

require __DIR__ . '/auth.php';
