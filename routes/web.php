<?php

use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\CheckoutController as AdminCheckouts;
use App\Http\Controllers\Admin\DiscountController as AdminDiscount;

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

//midtrans routes

Route::get('payment/success', [CheckoutController::class, 'midtransCallback']);
Route::post('payment/success', [CheckoutController::class, 'midtransCallback']);



Route::middleware(['auth'])->group(function () {
    //checkout routes
    Route::get('/checkouts/success', [CheckoutController::class, 'success'])->name('success_checkouts')->middleware('ensureUserRole:user');
    Route::get('/checkouts/{camp:slug}', [CheckoutController::class, 'create'])->name('checkouts')->middleware('ensureUserRole:user');
    Route::post('/checkouts/{camp}', [CheckoutController::class, 'store'])->name('checkouts.store')->middleware('ensureUserRole:user');

    //dashboard
    Route::get('dashboard', [CheckoutController::class, 'dashboard'])->name('dashboard');

    //user dashboard
    Route::prefix('user/dashboard')->namespace('User')->name('user.')->middleware('ensureUserRole:user')->group(function(){
        Route::get('/', [UserDashboard::class, 'index'])->name('dashboard');
    });

    //admin dashboard
    Route::prefix('admin/dashboard')->name('admin.')->middleware('ensureUserRole:admin')->group(function(){
        //dashboard
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

        //admin set to paid
        Route::post('checkout/{checkout}', [AdminCheckouts::class, 'update'])->name('checkout.update');

        //admin discount
        Route::resource('discount', AdminDiscount::class);
    });
});

Route::get('sign-in', [UserController::class, 'google'])->name('user.login.google');
Route::get('auth/google/callback', [UserController::class, 'handleProviderCallback'])->name('auth.google.callback');

require __DIR__ . '/auth.php';
