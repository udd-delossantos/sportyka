<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsStaff;
use App\Http\Middleware\IsCustomer;

use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\DailyOperationController;

use App\Http\Controllers\Admin\BookingController as AdminBookingController;

use App\Http\Controllers\Staff\WalkInSessionController;

use App\Http\Controllers\Staff\PaymentController;

use App\Http\Controllers\Staff\GameSessionController;

use App\Http\Controllers\Staff\QueueController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;



Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot-password');

// Forgot Password (request reset link)
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

// Reset Password (via email link)
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');


Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);


Route::post('/login', [LoginController::class, 'login']);



// Logout route (must be POST)
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');



Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')   
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('courts', \App\Http\Controllers\Admin\CourtController::class);
        Route::resource('daily-operations', \App\Http\Controllers\Admin\DailyOperationController::class);

        Route::get('daily-operations', [DailyOperationController::class, 'index'])->name('daily_operations.index');
        Route::post('daily-operations/open', [DailyOperationController::class, 'open'])->name('daily_operations.open');
        Route::post('daily-operations/{id}/close', [DailyOperationController::class, 'close'])->name('daily_operations.close');
        Route::post('daily-operations/reset', [DailyOperationController::class, 'reset'])->name('daily_operations.reset');
        Route::get('daily-operations/{id}', [DailyOperationController::class, 'show'])->name('daily_operations.show');
        Route::patch('/daily-operations/{id}/reopen', [DailyOperationController::class, 'reopen'])->name('daily-operations.reopen');

        Route::get('game-sessions', [\App\Http\Controllers\Admin\GameSessionController::class, 'index'])->name('game_sessions.index');

        Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('queues', [\App\Http\Controllers\Admin\QueueController::class, 'index'])->name('queues.index');
        Route::get('bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::get('booking_requests', [\App\Http\Controllers\Admin\BookingRequestController::class, 'index'])->name('booking_requests.index');


    });

Route::middleware(['auth', IsStaff::class])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard', [StaffDashboard::class, 'index'])->name('dashboard');

        Route::get('walk-ins', [WalkInSessionController::class, 'index'])->name('walk_ins.index');
        Route::get('walk-ins/create', [WalkInSessionController::class, 'create'])->name('walk_ins.create');
        Route::post('walk-ins', [WalkInSessionController::class, 'store'])->name('walk_ins.store');
        Route::post('walk-ins/{id}/start', [WalkInSessionController::class, 'start'])->name('walk_ins.start');
        Route::post('walk-ins/{id}/end', [WalkInSessionController::class, 'end'])->name('walk_ins.end');

        Route::get('game-sessions', [GameSessionController::class, 'index'])->name('game_sessions.index');
        Route::get('game-sessions/create', [GameSessionController::class, 'create'])->name('game_sessions.create');
        Route::post('game-sessions', [GameSessionController::class, 'store'])->name('game_sessions.store');
        Route::post('game-sessions/{id}/start', [GameSessionController::class, 'start'])->name('game_sessions.start');
        Route::post('game-sessions/{id}/end', [GameSessionController::class, 'end'])->name('game_sessions.end');
        Route::delete('game-sessions/{id}', [GameSessionController::class, 'destroy'])->name('game_sessions.destroy');


        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');

       
        Route::resource('bookings', \App\Http\Controllers\Staff\BookingController::class);
        Route::post('bookings/{id}/staerSession', [App\Http\Controllers\Staff\BookingController::class, 'startSession'])->name('bookings.startSession');




        Route::get('booking_requests', [\App\Http\Controllers\Staff\BookingRequestController::class, 'index'])->name('booking_requests.index');
        Route::post('booking_requests/{id}/approve', [\App\Http\Controllers\Staff\BookingRequestController::class, 'approve'])->name('booking_requests.approve');
        Route::post('booking_requests/{id}/cancel', [\App\Http\Controllers\Staff\BookingRequestController::class, 'cancel'])->name('booking_requests.cancel');



        Route::get('queues', [App\Http\Controllers\Staff\QueueController::class, 'index'])->name('queues.index');
        Route::get('queues/create', [App\Http\Controllers\Staff\QueueController::class, 'create'])->name('queues.create');
        Route::post('queues', [App\Http\Controllers\Staff\QueueController::class, 'store'])->name('queues.store');
        Route::post('queues/{id}/call', [App\Http\Controllers\Staff\QueueController::class, 'call'])->name('queues.call');
        Route::post('queues/{id}/skip', [App\Http\Controllers\Staff\QueueController::class, 'skip'])->name('queues.skip');

                // routes/web.php
        Route::get('/notifications', [\App\Http\Controllers\Staff\NotificationController::class, 'getNotifications'])
            ->name('notifications.count');



    });


Route::middleware(['auth' , 'verified', IsCustomer::class])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');



        Route::resource('bookings', \App\Http\Controllers\Customer\BookingController::class)->only(['index', 'create', 'store']);
        Route::resource('booking_requests', \App\Http\Controllers\Customer\BookingRequestController::class);

        Route::get('/courts/{court}', [\App\Http\Controllers\Customer\CourtController::class, 'show'])->name('courts.show');
        Route::get('/courts', [\App\Http\Controllers\Customer\CourtController::class, 'index'])->name('courts.index');

    });



Route::get('/', function () {
    return view('welcome');
});




Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 👇 when user clicks verification link
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    // role-based redirect after verification
    $user = $request->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'staff') {
        return redirect()->route('staff.dashboard');
    } elseif ($user->role === 'customer') {
        return redirect()->route('customer.dashboard');
    }

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__.'/auth.php';







