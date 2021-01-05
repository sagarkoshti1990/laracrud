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
	'navbar' => 'navbar-dark navbar-indigo',
	'sidebar' => 'sidebar-light-indigo',
	'text_color' => 'accent-indigo text-sm',
	'brand_logo' => 'navbar-indigo text-center text-white',
	'show_bg' => 'bg-indigo',
	/*
		navbar : navbar-light navbar-[color], navbar-dark navbar-[color]
		sidebar : sidebar-light-[color], sidebar-dark-[color]
		text_color : accent-[color]
		brand_logo : navbar-[color]
		color : primary, warning, info, danger, success, indigo, lightblue, navy, purple, fuchsia, pink,
				maroon, orange, lime, teal, olive
	*/

	// represent_attr change by module
	'represent_attr' => [
		// 'module_name' => [represent_attr,represent_attr2],
	],
	'access_show' => [],
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
	'layout_path' => 'stlc::layouts.app',
	
	'view_path' => [
		// 'Modules' => ['show'=>'test']
	],

	// buttons := preview,clone,update,delete,restore,permanently_delete,create
	'buttons' => [
		'preview' => false,//['stack' => 'line', 'name' => 'preview', 'type' => 'view', 'content' => 'test']
	],

    // Set this to false if you would like to use your own AuthController and PasswordController
    // (you then need to setup your auth routes manually in your routes.php file)
    'setup_auth_routes' => true,

    // Set this to false if you would like to skip adding the dashboard routes
    // (you then need to overwrite the login route on your AuthController)
    'setup_dashboard_routes' => true,

    // Set this to false if you would like to skip adding "my account" routes
    // (you then need to manually define the routes in your web.php)
    'setup_my_account_routes' => true,

    'restrictedModules' => [
        'menu' => [
            'Uploadables',
        ],
        'routeAdmin' => [
            'Uploadables',
        ]
    ],

    'generateMenu' => [
        ["name" => "Dashboard","label" => 'My Dashboard',"link" => "dashboard","icon" => "fa fa-tachometer-alt","type" => 'custom'],
        ["name" => "Profile","label" => 'Profile',"link" => "#", "icon" => "fa fa-user-secret","type" => 'custom',"hierarchy" => 1,
            'childMenu' => ["Users","Roles"]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    */
    // Fully qualified namespace of the User model
	'user_model' => \App\User::class,
	'access_module_model' => \Sagartakle\Laracrud\Models\AccessModule::class,
	'activity_model' => \Sagartakle\Laracrud\Models\Activity::class,
	'field_model' => \Sagartakle\Laracrud\Models\Field::class,
	'field_type_model' => \Sagartakle\Laracrud\Models\FieldType::class,
	'menu_model' => \Sagartakle\Laracrud\Models\Menu::class,
	'module_model' => \Sagartakle\Laracrud\Models\Module::class,
	'role_model' => \Sagartakle\Laracrud\Models\Role::class,
	'upload_model' => \Sagartakle\Laracrud\Models\Upload::class,
	'custom_helper' => \Sagartakle\Laracrud\Helpers\CustomHelper::class,

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
        
    // File manager
    // size in mb
    'file_upload_size' => '512',
    'file_manager_modal' => true,
    'file_modal_paginate_count' => 16,
	/*
	|--------------------------------------------------------------------------
	| Auto Set User ID
	|--------------------------------------------------------------------------
	|
	| If false, user ID will not be automatically set.
	|
	*/
	'auto_set_user_id' => true,

	/*
	|--------------------------------------------------------------------------
	| Auth Method
	|--------------------------------------------------------------------------
	|
	| If you are using any alternative packages for Authentication and User
	| management then you can put in the appropriate function to get
	| the currently logged in user.
	|
	| For example, if you are using Sentry, you would put Sentry::getUser()
	| instead of Laravel's default which is Auth::user().
	|
	*/
	'auth_method' => '\Auth::user',

	/*
	|--------------------------------------------------------------------------
	| Default Values
	|--------------------------------------------------------------------------
	|
	| The default values of certain fields. If you would like to use the
	| language key system by default, add another default value for
	| "language_key" and set it to true. You may also add one for "public" if
	| you intend for logged activities to be made public by default.
	|
	*/
	'defaults' => [
		'action' => 'Create',
	],

	/*
	|--------------------------------------------------------------------------
	| Language Key Settings
	|--------------------------------------------------------------------------
	|
	| "prefixes.replacements" is the language key prefix for replacements within
	| a language string. For example, setting it to "labels" will allow you to
	| use "article" to get "labels.article". The other two prefix config
	| variables are related to the "description" and "details" fields.
	|
	*/
	'language_key' => [
		'prefixes' => [
			'descriptions' => 'activity-log::descriptions',
			'details'      => 'activity-log::details',
			'replacements' => null,
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| The "developer" is the name of users for logged activities that have the
	| "developer" flag set. "unknown" is for logged activities that do not have
	| an associated user.
	|
	*/
	'names' => [
		'developer' => '',
		'unknown'   => 'Unknown User',
	],

	/*
	|--------------------------------------------------------------------------
	| Full Name as Name
	|--------------------------------------------------------------------------
	|
	| If "full_name_as_name" is true, the "first_name" and "last_name" attributes
	| are concantenated together, separated by a space. If false, the
	| "username" attribute of the user is used as the name instead. If
	| "full_name_last_name_first" is set, the name will be displayed like
	| "Smith, John" instead of "John Smith".
	|
	*/
	'full_name_as_name'         => true,
	'full_name_last_name_first' => false,

	/*
	|--------------------------------------------------------------------------
	| Action Icons
	|--------------------------------------------------------------------------
	|
	| The icons for specific actions. The defaults point to various icons in
	| the Font Awesome set.
	|
	*/
	'action_icon' => [
		'element'      => 'i',
		'class_prefix' => 'fa fa-',
	],

	'action_icons' => [
		'x'          => 'info-circle',
		'created'     => 'plus-circle',
		'add'        => 'plus-circle',
		'post'       => 'plus-circle',
		'updated'     => 'edit',
		'deleted'     => 'minus-circle',
		'remove'     => 'minus-circle',
		'upload'     => 'cloud-upload-alt',
		'download'   => 'cloud-download-alt',
		'ban'        => 'ban',
		'unban'      => 'circle-notch',
		'approve'    => 'check-circle',
		'unapprove'  => 'ban',
		'activate'   => 'check-circle',
		'deactivate' => 'ban',
		'log_in'     => 'sign-in-alt',
		'log_out'    => 'sign-out-alt',
		'view'       => 'eye',
		'open'       => 'eye',
		'comment'    => 'comment',
		'mail'       => 'envelope',
		'email'      => 'envelope',
		'send'       => 'envelope',
	],

	'action_icon_bd_colors' => [
		'x'          => 'bg-red',
		'created'     => 'bg-green',
		'add'        => 'bg-green',
		'post'       => 'bg-green',
		'updated'     => 'bg-orange',
		'deleted'     => 'bg-red',
		'remove'     => 'bg-red',
		'upload'     => 'bg-green',
		'download'   => 'bg-green',
		'ban'        => 'bg-red',
		'unban'      => 'bg-green',
		'approve'    => 'bg-green',
		'unapprove'  => 'bg-red',
		'activate'   => 'bg-green',
		'deactivate' => 'bg-red',
		'log_in'     => 'bg-green',
		'log_out'    => 'bg-red',
		'view'       => 'bg-olive',
		'open'       => 'bg-olive',
		'comment'    => 'bg-orange',
		'mail'       => 'bg-orange',
		'email'      => 'bg-orange',
		'send'       => 'bg-orange',
	],

	/*
	|--------------------------------------------------------------------------
	| view atributes class icon
	|--------------------------------------------------------------------------
	| custome class atribute of stlc view content 
	|
	*/
	
	'view' => [
		'attributes' => [
			// 'card' => ['class'=>'card bg-green'],
			// 'table' => ['class'=>'table display crudTable table-dark'],
			// 'thead' => ['class'=>'thead-light'],
			// 'th_action_attributes' => ["style" => "max-width: 150px;"],
			// 'th_id_attributes' => ['style' => 'max-width:10px'],
			// 'th_attributes' => ['style' => 'max-width: 50px;'],
			
			// 'button' => [
			// 'clone' => ['class'=>'btn btn-flat btn-sm mb-2 bg-gray'],
			// 	'create' => ['class'=>'btn btn-primary btn-lg position-fixed'],
			// 	'delete' => ['class'=>'btn btn-flat btn-sm mb-2 bg-maroon'],
			// 	'edit' => ['class'=>'btn btn-flat btn-sm mb-2 bg-orange'],
			// 	'permanently_delete' => ['class'=>'btn btn-flat btn-sm mb-2 bg-danger'],
			// 	'preview' => ['class'=>'btn btn-flat btn-sm mb-2 bg-purple'],
			// 	'restore' => ['class'=>'btn btn-flat btn-sm mb-2 bg-purple'],
			// ],
			// 'form' => ['autocomplete'=>"off"],
			
			// 'index' => [
			// ],
			// 'form' => [
			// ],
			// 'show' => [
			// ],
		],

		// 'icon' => [
		// 	'show_info' => 'fa fa-user mr-2',
		// 	'show_arrow_left' => 'fa fa-user mr-2',
		// 	'show_log' => 'fa fa-user mr-2',
		// 	'show_access' => 'fa fa-user mr-2',
		// 	'button' => [
		// 		'clone' => 'fa fa-user',
		// 		'delete' => 'fa fa-user',
		// 		'edit' => 'fa fa-user',
		// 		'preview' => 'far fa-user',
		// 		'permanently_delete' => 'fa fa-user',
		// 		'create' => 'fa fa-user',
		// 		'restore' => 'fa fa-user',
		// 	],
		// ],

		// 'size' => [
		// 	'file_show_size' => '35px',
		// 	'file_card_size' => '200px',
		// 	'image_show_size' => '50',
		// ],
	],
];
