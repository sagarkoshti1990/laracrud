<?php

namespace Sagartakle\Laracrud\Controllers;

use Sagartakle\Laracrud\Helpers\Oprations\Show;
use Sagartakle\Laracrud\Helpers\Oprations\Index;
use Sagartakle\Laracrud\Helpers\Oprations\Store;
use Sagartakle\Laracrud\Helpers\Oprations\Update;
use Sagartakle\Laracrud\Helpers\Oprations\Destroy;
use Sagartakle\Laracrud\Helpers\Oprations\Restore;
use App\Http\Controllers\Controller as CrudController;

class StlcController extends CrudController
{
    use Index,Store,Update,Show,Destroy,Restore;
}
