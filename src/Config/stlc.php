<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Look & feel customizations
    |--------------------------------------------------------------------------
    |
    | Make it yours.
    |
    */
    // The AdminLTE skin. Affects menu color and primary/secondary colors used throughout the application.
    'skin' => 'skin-purple',
    // Options: skin-black, skin-blue, skin-purple, skin-red, skin-yellow, skin-green, skin-blue-light, skin-black-light, skin-purple-light, skin-green-light, skin-red-light, skin-yellow-light

    /*
    |--------------------------------------------------------------------------
    | Registration Open
    |--------------------------------------------------------------------------
    |
    | Choose whether new users are allowed to register.
    | This will show up the Register button in the menu and allow access to the
    | Register functions in AuthController.
    |
    */

    'registration_open' => (env('APP_ENV') == 'local') ? true : false,
    'app_debug' => env('APP_DEBUG',false),
    
    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    */

    // The prefix used in all base routes (the 'admin' in admin/dashboard)
    'route_prefix' => 'admin',
    'route_group_middleware_all' => 'auth',
    'route_group_namespace' => '\App\Http\Controllers\Admin',

    //stlc
    'stlc_route_prefix' => 'developer',
    'stlc_route_group_middleware' => 'auth',
    'stlc_modules_folder_name' => 'stlc::',

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    */
    // Fully qualified namespace of the User model
    'user_model_fqn' => '\App\User',

    /*
    |--------------------------------------------------------------------------
    | stlc\CRUD preferences
    |--------------------------------------------------------------------------
    */

    /*
    |------------
    | READ
    |------------
    */
    // LIST VIEW (table view)
        // How many items should be shown by default by the Datatable?
        'default_page_length' => 10,
];
