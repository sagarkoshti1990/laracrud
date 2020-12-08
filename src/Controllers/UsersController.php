<?php

namespace Sagartakle\Laracrud\Controllers;

use Sagartakle\Laracrud\Controllers\StlcController;
use Sagartakle\Laracrud\Models\Role;

class UsersController extends StlcController
{
    function __construct() {
        $this->crud = \Module::make('Users');
    }
    
    // write custom function or override function.
}
