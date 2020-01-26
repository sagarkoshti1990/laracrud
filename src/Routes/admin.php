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
    'prefix'     => config('stlc.route_prefix', 'admin'),
    'middleware' => config('stlc.route_group_middleware_all', 'auth'),
    'namespace'  => config('stlc.route_group_namespace', 'Admin'),
], function () {
    
    if(Schema::hasTable('modules')) {
        $modules = \Sagartakle\Laracrud\Models\Module::whereNotIn('name',['Users','Roles','Uploads'])->get();
        if(isset($modules) && count($modules)) {
            foreach ($modules as $key => $module) {
                Crud::resource($module->table_name, $module->controller);
            }
        }
    }
    
});

Route::group([
    'prefix'     => config('stlc.stlc_route_prefix', 'developer'),
    'middleware' => config('stlc.stlc_route_group_middleware', 'auth'),
    'namespace'  => '\Sagartakle\Laracrud\Controllers',
], function () {
    // modules
    Crud::resource("modules", "ModulesController");
    Crud::resource('roles', 'RolesController');
    Crud::resource('upload', 'UploadsController');
    Route::get('data_select', 'ModulesController@select2');
    Route::get('fields', 'ModulesController@index');
    Route::post('fields', 'ModulesController@add_field');
    Route::get('fields/{id}', 'ModulesController@show_field');
    Route::get('fields/{id}/edit', 'ModulesController@edit_field');
    Route::put('fields/{id}', 'ModulesController@update_field');
    Route::delete('fields/{id}', 'ModulesController@destroy_field');

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
});