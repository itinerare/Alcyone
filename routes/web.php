<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Controller;
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

Route::controller(Controller::class)->prefix('info')->group(function () {
    Route::get('terms', 'getTerms');
    Route::get('privacy', 'getPrivacyPolicy');
});

/* Routes that require login */
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::controller(Controller::class)->group(function () {
        Route::get('/', 'getIndex');
    });

    Route::controller(AccountController::class)->group(function () {
        Route::prefix('account')->group( function () {
            Route::get('settings', 'getSettings');
            Route::post('profile', 'postProfile');
            Route::post('password', 'postPassword');
            Route::post('email', 'postEmail');
            Route::post('avatar', 'postAvatar');

            Route::get('two-factor/confirm', 'getConfirmTwoFactor');
            Route::post('two-factor/enable', 'postEnableTwoFactor');
            Route::post('two-factor/confirm', 'postConfirmTwoFactor');
            Route::post('two-factor/disable', 'postDisableTwoFactor');
        });

        Route::prefix('notifications')->group( function () {
            Route::get('/', 'getNotifications');
            Route::get('delete/{id}', 'getDeleteNotification');
            Route::post('clear', 'postClearNotifications');
            Route::post('clear/{type}', 'postClearNotifications');
        });
    });

    /* Routes that require moderator permissions */
    Route::group(['middleware' => ['mod']], function () {
        //

        /* Routes that require admin permissions */
        Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['admin']], function () {
            //
        });
    });
});
