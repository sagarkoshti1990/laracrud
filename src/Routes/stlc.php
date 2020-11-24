<?php
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('files/{hash}/{name}', '\Sagartakle\Laracrud\Controllers\UploadsController@get_file');

Route::group([
    'prefix'     => config('stlc.route_prefix', 'admin'),
    'middleware' => ['web',config('stlc.route_group_middleware_all', 'auth')],
    'namespace'  => '\\',
], function () {
    if(Schema::hasTable('modules')) {
        $modules = config('stlc.module_model')::whereNotIn('name',config('stlc.restrictedModules.routeAdmin',['Users','Uploads','Roles']))->get();
        if(isset($modules) && count($modules)) {
            foreach ($modules as $key => $module) {
                Crud::resource($module->table_name, $module->controller);
            }
        }
    }
});

Route::group([
    'prefix'     => config('stlc.route_prefix', 'admin'),
    'middleware' => ['web',config('stlc.route_group_middleware_all', 'auth')],
    'namespace'  => '\Sagartakle\Laracrud\Controllers',
], function () {
    Route::get('dashboard','ModulesController@dashboard');
    Route::get('/','ModulesController@dashboard');
});

Route::group([
    'prefix'     => config('stlc.stlc_route_prefix', 'developer'),
    'middleware' => ['web',config('stlc.stlc_route_group_middleware', 'auth')],
    'namespace'  => '\Sagartakle\Laracrud\Controllers',
], function () {
    
    // modules
    Crud::resource("modules", "ModulesController");
    Crud::resource('roles', 'RolesController');
    Crud::resource('uploads', 'UploadsController');
    Route::get('data_select', 'ModulesController@select2');
    Route::get('fields', 'ModulesController@index');
    Route::post('fields', 'ModulesController@add_field');
    Route::get('fields/{id}', 'ModulesController@show_field');
    Route::get('fields/{id}/edit', 'ModulesController@edit_field');
    Route::put('fields/{id}', 'ModulesController@update_field');
    Route::delete('fields/{id}', 'ModulesController@destroy_field');
    Route::get('table', 'ModulesController@crudTable');
    Route::post('getModuleData', 'ModulesController@getModuleData');

    Route::post('context/{id}/comment', 'ModulesController@comment');
    Route::post('context/{id}/comment_history', 'ModulesController@comment_history');
    
    // modules
    Route::resource("modules", "ModulesController");
    Route::post('employees/access/{id}', 'ModulesController@module_permissions');
    Route::post('roles/access/{id}', 'ModulesController@module_permissions');

    // activity
    Route::resource("activity_log", "ActivitiesController", ['only' => ['index']]);
    Route::post('activity_log/datatable', [
        'as' => 'crud.activities.datatable',
        'uses' => 'ActivitiesController@datatable',
    ]);
    Route::post('activity_log/get_data/{id}', 'ActivitiesController@get_data');
    Route::post('activities', 'ActivitiesController@get_data_ajax');

    //uploads custom
    Route::post('/upload_files', 'UploadsController@upload_files');
    Route::get('/uploaded_files', 'UploadsController@uploaded_files');
    Route::post('/uploads_update_caption', 'UploadsController@update_caption');
    Route::post('/uploads_update_filename', 'UploadsController@update_filename');
    Route::post('/uploads_update_public', 'UploadsController@update_public');
    Route::post('/uploads_delete_file', 'UploadsController@delete_file');

    // if not otherwise configured, setup the "my account" routes
    Route::get('edit-account-info', 'Auth\MyAccountController@getAccountInfoForm')->name('stlc.account.info');
    Route::post('edit-account-info', 'Auth\MyAccountController@postAccountInfoForm');
    Route::get('change-password', 'Auth\MyAccountController@getChangePasswordForm')->name('stlc.account.password');
    Route::post('change-password', 'Auth\MyAccountController@postChangePasswordForm');
});

Route::group([
    'namespace'  => '\Sagartakle\Laracrud\Controllers\Auth',
    'middleware' => 'web',
    'prefix'     => config('stlc.route_prefix', 'admin'),
],function () {
    // if not otherwise configured, setup the auth routes
    // Authentication Routes...
    Route::get('login', [
        'as' => 'login',
        'uses' => 'LoginController@showLoginForm'
    ]);
    Route::post('login', [
        'as' => '',
        'uses' => 'LoginController@login'
    ]);
    Route::get('logout', [
        'as' => 'logout',
        'uses' => 'LoginController@logout'
    ]);

    // Password Reset Routes...
    Route::post('password/email', [
        'as' => 'password.email',
        'uses' => 'ForgotPasswordController@sendResetLinkEmail'
    ]);
    Route::get('password/reset', [
        'as' => 'password.request',
        'uses' => 'ForgotPasswordController@showLinkRequestForm'
    ]);
    Route::post('password/reset', [
        'as' => 'password.update',
        'uses' => 'ResetPasswordController@reset'
    ]);
    Route::get('password/reset/{token}', [
        'as' => 'password.reset',
        'uses' => 'ResetPasswordController@showResetForm'
    ]);

    // Registration Routes...
    Route::get('register', [
    'as' => 'register',
    'uses' => 'RegisterController@showRegistrationForm'
    ]);
    Route::post('register', [
    'as' => 'register.store',
    'uses' => 'RegisterController@register'
    ]);

    // if not otherwise configured, setup the dashboard routes
    // Route::get('dashboard', 'AdminController@dashboard');
    // Route::get('/', 'AdminController@redirect');
});