<?php

use App\Mail\Email;
use App\Livewire\Login;
use App\Livewire\Booking;
use App\Livewire\Dashboard;
use App\Livewire\ManageRoom;
use App\Livewire\ManageUser;
use App\Livewire\ManageOrder;
use App\Livewire\ManageProduct;
use App\Livewire\WalkInCheckIn;
use App\Livewire\ManageRoomType;
use App\Livewire\GuestRoomBooking;
use App\Livewire\ManageGuestBooking;
use Illuminate\Support\Facades\Mail;
use App\Livewire\ManageCheckInAndOut;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Livewire\OrderReports;
use App\Livewire\PosReports;
use App\Livewire\TraininVideos;
use App\Livewire\Transactions;

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

Route::redirect('/', '/dashboard');

Route::group(['middleware' => 'auth'], function(){

// Admin Route
Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/roomtype', ManageRoomType::class)->name('roomtype');
Route::get('/rooms/{status?}', ManageRoom::class)->name('rooms');
Route::get('/booking', Booking::class)->name('booking');
Route::get('/guest-record', ManageGuestBooking::class)->name('guest-record');

Route::get('/checkInGuest/{byStatus?}', ManageCheckInAndOut::class)->name('checkInGuest');
Route::get('/walk-in-check-in', WalkInCheckIn::class)->name('walk-in-check-in');
Route::get('/product', ManageProduct::class)->name('product');
Route::get('/manage-order/{bookId}', ManageOrder::class)->name('manage-order')->middleware('checkOrderOwnership');

// Reports
Route::get('/transaction/{status?}/{date?}', Transactions::class)->name('transaction');
Route::get('/order-reports', OrderReports::class)->name('order-reports');
Route::get('/pos-reports/{date?}', PosReports::class)->name('pos-reports');

// Guest Route
Route::get('/home', GuestRoomBooking::class)->name('home');
});

Route::middleware(['admin'])->group(function () {
    Route::get('/manage-user', ManageUser::class)->name('manage-user');
    Route::get('/training-videos', TraininVideos::class)->name('training-videos');
});


Route::group(['middleware' => 'guest'], function(){
Route::get('/login', function(){
    return view('login-form.login');
});
Route::post('/login', [AuthController::class, 'login'])->name('login');
});