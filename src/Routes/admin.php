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

Route::group([
    'prefix'     => config('lara.base.route_prefix', 'admin'),
    'middleware' => ['admin'],
    'namespace'  => 'Admin',
], function () {
    
    if(Schema::hasTable('modules')) {
        $modules = \App\Models\Module::all();
        if(isset($modules) && count($modules)) {
            foreach ($modules as $key => $module) {
                if(!in_array($module->name, ['Users','Permissions','Notifications','Settings','Devicetokens'])) {
                    Crud::resource($module->table_name, $module->controller);
                }
            }
        }
    }
    
    // if not otherwise configured, setup the "my account" routes
    if (config('lara.base.setup_my_account_routes')) {
        Route::get('edit-account-info', 'Auth\MyAccountController@getAccountInfoForm')->name('lara.account.info');
        Route::post('edit-account-info', 'Auth\MyAccountController@postAccountInfoForm');
        Route::get('change-password', 'Auth\MyAccountController@getChangePasswordForm')->name('lara.account.password');
        Route::post('change-password', 'Auth\MyAccountController@postChangePasswordForm');
    }
    
    // modules
    Crud::resource("modules", "ModulesController");
    Route::get('data_select', 'ModulesController@select2');
    Route::get('fields', 'ModulesController@index');
    Route::post('fields', 'ModulesController@add_field');
    Route::get('fields/{id}', 'ModulesController@show_field');
    Route::get('fields/{id}/edit', 'ModulesController@edit_field');
    Route::put('fields/{id}', 'ModulesController@update_field');
    Route::delete('fields/{id}', 'ModulesController@destroy_field');
    Route::get('settings', 'ModulesController@setting_list');
    Route::post('settings', 'ModulesController@setting_store');
    Route::get('settings/{id}/edit', 'ModulesController@setting_edit');
    Route::put('settings/{id}', 'ModulesController@setting_update');

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

    // migration data of relis one
    Route::get('migration','MasterUsersController@migration');
    Route::Post('migrationUserSave','MasterUsersController@migrationUser');

    // import export
    Route::get('import/events', 'EventsController@import');
    // Route::Post('import/events', 'EventsController@import_store');
    Route::get('export/events', 'EventsController@export');
});
