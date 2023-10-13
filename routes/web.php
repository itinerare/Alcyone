<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\RankController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
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

Route::controller(Controller::class)->group(function () {
    Route::prefix('info')->group(function () {
        Route::get('terms', 'getTerms');
        Route::get('privacy', 'getPrivacyPolicy');
    });

    Route::get('images/converted/{slug}', 'getConvertedImage')
        ->whereAlphaNumeric('slug');
});

/* Routes that require login */
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::controller(Controller::class)->group(function () {
        Route::get('/', 'getIndex');

        Route::prefix('images')->group(function () {
            Route::get('view/{slug}', 'getImage')
                ->whereAlphaNumeric('slug');
            Route::post('upload', 'postUploadImage');
            Route::post('delete/{slug}', 'postDeleteImage')
                ->whereAlphaNumeric('slug');
        });
    });

    Route::controller(AccountController::class)->group(function () {
        Route::prefix('account')->group(function () {
            Route::get('settings', 'getSettings');
            Route::post('theme', 'postTheme');
            Route::post('email', 'postEmail');
            Route::post('password', 'postPassword');

            Route::get('two-factor/confirm', 'getConfirmTwoFactor');
            Route::post('two-factor/enable', 'postEnableTwoFactor');
            Route::post('two-factor/confirm', 'postConfirmTwoFactor');
            Route::post('two-factor/disable', 'postDisableTwoFactor');
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', 'getNotifications');
            Route::get('delete/{id}', 'getDeleteNotification');
            Route::post('clear', 'postClearNotifications');
            Route::post('clear/{type}', 'postClearNotifications');
        });
    });

    /* Routes that require moderator permissions */
    Route::group(['middleware' => ['mod']], function () {
        Route::prefix('admin')->group(function () {
            Route::controller(AdminController::class)->group(function () {
                Route::get('/', 'getIndex');
            });

            Route::controller(ReportController::class)->prefix('reports')->group(function () {
                Route::get('/', 'getReportIndex');
                Route::get('/{status}', 'getReportIndex')->where('status', 'pending|processed');
            });

            /* Routes that require admin permissions */
            Route::group(['middleware' => ['admin']], function () {
                Route::controller(RankController::class)->prefix('ranks')->group(function () {
                    Route::get('/', 'getIndex');
                    Route::get('edit/{id}', 'getEditRank');
                    Route::post('edit/{id?}', 'postEditRank');
                });

                Route::controller(InvitationController::class)->prefix('invitations')->group(function () {
                    Route::get('/', 'getIndex');
                    Route::post('create', 'postGenerateKey');
                    Route::post('delete/{id?}', 'postDeleteKey');
                });

                Route::controller(UserController::class)->prefix('users')->group(function () {
                    Route::get('/', 'getUserIndex');
                    Route::get('{name}/edit', 'getUser');
                    Route::get('{name}/updates', 'getUserUpdates');
                    Route::post('{name}/basic', 'postUserBasicInfo');
                    Route::post('{name}/account', 'postUserAccount');

                    Route::get('{name}/ban', 'getBan');
                    Route::get('{name}/ban-confirm', 'getBanConfirmation');
                    Route::post('{name}/ban', 'postBan');
                    Route::get('{name}/unban-confirm', 'getUnbanConfirmation');
                    Route::post('{name}/unban', 'postUnban');
                });
            });
        });
    });
});
