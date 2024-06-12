<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

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

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])
//    ->middleware('customThrottle:3,2,1') // 3 attempts in 2 minutes, block for 1 minute
    ->name('login')->middleware('throttle.login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/home1', function () {
    return view('home1');
})->name('home1')->middleware('auth');

Route::get('/home2', function () {
    return view('home2');
})->name('home2')->middleware('auth');
