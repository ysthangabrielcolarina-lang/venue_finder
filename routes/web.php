<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Venues: owners and admins manage them. This group must come BEFORE the public
// routes, otherwise /venues/create would be read as /venues/{venue}.
Route::middleware(['auth', 'role:owner,admin'])->group(function () {
    Route::resource('venues', VenueController::class)->except(['index', 'show']);
});

// Anyone can browse.
Route::resource('venues', VenueController::class)->only(['index', 'show']);

// Reservations: only customers can make one.
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/venues/{venue}/reserve', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/venues/{venue}/reservations', [ReservationController::class, 'store'])->name('reservations.store');
});

// Everyone logged in can reach these. The ReservationPolicy decides who may do what.
Route::middleware('auth')->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::get('/reservations/{reservation}/receipt', [ReservationController::class, 'receipt'])->name('reservations.receipt');
    Route::patch('/reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::patch('/reservations/{reservation}/reject', [ReservationController::class, 'reject'])->name('reservations.reject');
    Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', fn () => 'Admin area')->name('admin.index');
});

Route::middleware(['auth', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/', fn () => 'Owner area')->name('owner.index');
});

require __DIR__.'/auth.php';