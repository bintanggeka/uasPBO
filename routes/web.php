<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\RentalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home page
Route::get('/', function () {
    return view('home');
});

// Consoles page
Route::get('/consoles', function () {
    return view('consoles');
});

// Games page
Route::get('/games', function () {
    return view('games');
});

// Pricing page
Route::get('/pricing', function () {
    return view('pricing');
});

// Contact page
Route::get('/contact', function () {
    return view('contact');
});

Route::resource('consoles', ConsoleController::class);

Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
Route::get('/rentals/create', [RentalController::class, 'create'])->name('rentals.create');
Route::post('/rentals', [RentalController::class, 'store'])->name('rentals.store');
Route::get('/rentals/{rental}', [RentalController::class, 'show'])->name('rentals.show');
Route::patch('/rentals/{rental}/status', [RentalController::class, 'updateStatus'])->name('rentals.update-status');
