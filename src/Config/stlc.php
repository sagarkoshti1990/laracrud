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

    // Date & Datetime Format Carbon
    'default_date_format'     => 'j F Y',
    'default_datetime_format' => 'j F Y H:i',

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

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    */

    // The prefix used in all base routes (the 'admin' in admin/dashboard)
    'route_prefix' => 'admin',
    'route_group_middleware_all' => 'auth',
    'route_group_namespace' => 'Admin',

    //stlc
    'stlc_route_prefix' => 'developer',
    'stlc_route_group_middleware' => 'auth',

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    */

    // Fully qualified namespace of the User model
    'user_model_fqn' => '\App\User',

    'setting_keys' => [
        ['key' => "COMPANY_NAME", "type" => "Text"],
        ['key' => "COMPANY_LOGO", "type" => "Image"]
    ],

    /*
    |--------------------------------------------------------------------------
    | propadmin\CRUD preferences
    |--------------------------------------------------------------------------
    */

    /*
    |------------
    | READ
    |------------
    */
    // LIST VIEW (table view)
        // How many items should be shown by default by the Datatable?
        // This value can be overwritten on a specific CRUD by calling
        // $this->crud->setDefaultPageLength(50);
        'default_page_length' => 10,

    // PREVIEW

    /*
    |------------
    | DELETE
    |------------
    */

    /*
    |------------
    | REORDER
    |------------
    */

    /*
    |------------
    | DETAILS ROW
    |------------
    */

    /*
    |-------------------
    | TRANSLATABLE CRUDS
    |-------------------
    */

];
