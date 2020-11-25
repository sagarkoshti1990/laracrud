<?php

namespace Sagartakle\Laracrud\Controllers;

use Sagartakle\Laracrud\Controllers\StlcController;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\Role;

class UsersController extends StlcController
{
    function __construct() {
        $this->crud = config('stlc.module_model')::make('Users');
    }
    
    // write custom function or override function.
}
