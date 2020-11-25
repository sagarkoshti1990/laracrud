<?php

namespace Sagartakle\Laracrud\Controllers;

use Sagartakle\Laracrud\Controllers\StlcController;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\Role;

class RolesController extends StlcController
{
    function __construct() {
        $this->crud = config('stlc.module_model')::make('Roles');
    }
    
    // write custom function or override function.
}
