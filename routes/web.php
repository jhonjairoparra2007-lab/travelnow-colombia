<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

Route::get('/', [HotelController::class, 'index'])->name('hotels.index');
Route::post('/reservar', [HotelController::class, 'storeReserve'])->name('hotels.reserve');

