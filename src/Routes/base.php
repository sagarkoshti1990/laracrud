<?php

/*
|--------------------------------------------------------------------------
| lara\Base Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the lara\Base package.
|
*/

Route::group(
[
    'namespace'  => 'App\Http\Controllers',
    'middleware' => 'web',
    'prefix'     => config('lara.base.route_prefix'),
],
function () {
    // if not otherwise configured, setup the auth routes
    if (config('lara.base.setup_auth_routes')) {
        Route::auth();
        Route::get('logout', 'Auth\LoginController@logout');
    }

    // if not otherwise configured, setup the dashboard routes
    if (config('lara.base.setup_dashboard_routes')) {
        Route::get('dashboard', 'AdminController@dashboard');
        Route::get('/', 'AdminController@redirect');
    }
});

Route::group([
    'prefix'     => config('lara.base.route_prefix', 'admin'),
    'middleware' => ['admin'],
    'namespace'  => 'App\Http\Controllers',
], function () {
    
    // if not otherwise configured, setup the "my account" routes
    if (config('lara.base.setup_my_account_routes')) {
        Route::get('edit-account-info', 'Auth\MyAccountController@getAccountInfoForm')->name('lara.account.info');
        Route::post('edit-account-info', 'Auth\MyAccountController@postAccountInfoForm');
        Route::get('change-password', 'Auth\MyAccountController@getChangePasswordForm')->name('lara.account.password');
        Route::post('change-password', 'Auth\MyAccountController@postChangePasswordForm');
    }
});

