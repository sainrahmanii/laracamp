<?php

use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('layouts.index');
// })->name('index');

Route::get('/', [CheckoutController::class, 'index'])->name('index');



Route::middleware(['auth'])->group(function () {
    //checkout routes
    Route::get('/checkouts/success', [CheckoutController::class, 'success'])->name('success_checkouts');
    Route::get('/checkouts/{camp:slug}', [CheckoutController::class, 'create'])->name('checkouts');
    Route::post('/checkouts/{camp}', [CheckoutController::class, 'store'])->name('checkouts.store');

    //dashboard
    Route::get('dashboard-invoice', [CheckoutController::class, 'dashboard'])->name('dashboard.invoice');
    Route::get('dashboard/checkout/invoice/{checkout}', [CheckoutController::class, 'invoice'])->name('user.checkout.invoice');
});

Route::get('sign-in', [UserController::class, 'google'])->name('user.login.google');
Route::get('auth/google/callback', [UserController::class, 'handleProviderCallback'])->name('auth.google.callback');

require __DIR__ . '/auth.php';
