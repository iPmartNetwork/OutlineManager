<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KeyController;
use App\Http\Controllers\ServerController;
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

Route::as('auth.')
    ->middleware('guest')
    ->controller(AuthController::class)
    ->group(function () {
        Route::get('/', 'showLoginForm')->name('login.show');
        Route::post('/', 'login')->name('login.store');

        Route::delete('/', 'logout')->name('login.destroy')
            ->middleware('auth')
            ->withoutMiddleware('guest');
    });

Route::middleware('auth')->group(function () {
    Route::resource('servers', ServerController::class);
    Route::resource('servers.keys', KeyController::class);
});
