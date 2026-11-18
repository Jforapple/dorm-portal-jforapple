<?php

/**
 * Web routes for the Dorm Portal.
 *
 * Module map:
 *  - Room inspections  /roomcheck  RoomCheckController  (full resource, show() still empty)
 *  - Worship events    /events     EventController      (index, create, store only)
 *  - Worship check-in  —           WorshipController    (planned: attendance per event)
 *  - Cleaning duty     —           CleaningController   (planned)
 *  - Visits            —           VisitController      (planned)
 *  - Users             —           UserController       (planned: admin user management)
 *
 * Run `php artisan route:list --except-vendor` to see every registered route.
 *
 * TODO: Protect every module route with the `auth` middleware once login exists.
 */

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RoomCheckController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::resource('roomcheck', RoomCheckController::class);

// TODO: Extend to the full resource (show, edit, update, destroy).
Route::resource('events', EventController::class)->only([
    'index',
    'create',
    'store',
    'show',
    'edit',
    'update',
    'destroy',
]);
