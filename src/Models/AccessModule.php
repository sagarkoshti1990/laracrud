<?php

namespace Sagartakle\Laracrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Actuallymab\LaravelComment\Commentable;
// use Sagartakle\Laracrud\Helpers\Traits\ActivityTrait;

class AccessModule extends Model
{
    use SoftDeletes;
    // use Commentable;
    // use ActivityTrait;
     /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'access_modules';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];
    
    protected $mustBeApproved = false;
    protected $canBeRated = false;

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * get module info of this AccessModules.
     *
     * @param module
     *
     * @return void
     */
    // public Static function get_module()
    // {
    //     return \Module::where('name', 'AccessModules')->first();
    // }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * A model may have module.
     */
    public function module()
    {
        return $this->belongsTo('Sagartakle\Laracrud\\Module','module_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
