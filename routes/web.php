<?php

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

Route::get('/', function () {
    return view('layouts.index');
})->name('index');

Route::get('/checkouts', function() {
    return view('layouts.checkouts');
})->name('checkouts');

Route::get('/success-checkout', function() {
    return view('layouts.success_checkouts');
})->name('success_checkouts');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('sign-in', [UserController::class, 'google'])->name('user.login.google');
Route::get('auth/google/callback', [UserController::class, 'handleProviderCallback'])->name('auth.google.callback');

require __DIR__.'/auth.php';

