<?php

/**
 * Web routes for the Dorm Portal.
 *
 * Module map:
 *  - Room inspections  /roomcheck  RoomCheckController  (full resource, show() still empty)
 *  - Worship events    /events     EventController      (index, create, store only)
 *  - Worship attendance /attendance WorshipController   (student history and staff marking)
 *  - Cleaning duty     —           CleaningController   (planned)
 *  - Visits            —           VisitController      (planned)
 *  - Users             —           UserController       (planned: admin user management)
 *
 * Run `php artisan route:list --except-vendor` to see every registered route.
 */

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CleaningDutyController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomCheckController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorshipController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Protected module routes
Route::middleware('auth')->group(function () {
    Route::resource('roomcheck', RoomCheckController::class);

    Route::resource('events', EventController::class);

    Route::resource('users', UserController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/attendance', [WorshipController::class, 'index'])
        ->name('attendance.index');

    Route::get('/events/{event}/attendance', [WorshipController::class, 'show'])
        ->name('events.attendance');

    Route::post('/events/{event}/attendance', [WorshipController::class, 'store'])
        ->name('events.attendance.store');

    // Cleaning duty routes
    Route::get('/cleaning-duties', [CleaningDutyController::class, 'index'])
        ->name('cleaning-duties.index');

    Route::get('/cleaning-duties/create', [CleaningDutyController::class, 'create'])
        ->name('cleaning-duties.create');

    Route::post('/cleaning-duties', [CleaningDutyController::class, 'store'])
        ->name('cleaning-duties.store');

    Route::patch('/cleaning-duties/{cleaning_duty}/complete', [CleaningDutyController::class, 'complete'])
        ->name('cleaning-duties.complete');
});
