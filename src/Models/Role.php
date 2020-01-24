<?php

namespace Sagartakle\Laracrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Actuallymab\LaravelComment\Commentable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Sagartakle\Laracrud\Helpers\Traits\ActivityTrait;

class Role extends Model
{
    use SoftDeletes;
    // use Commentable;
    use ActivityTrait;
     /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'roles';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];
    
    protected $mustBeApproved = false;
    protected $canBeRated = false;

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * get module info of this Roles.
     *
     * @param module
     *
     * @return void
     */
    public Static function get_module()
    {
        return \Module::where('name', 'Roles')->first();
    }

    /**
     * get module info of this Roles.
     *
     * @param module
     *
     * @return void
     */
    public Static function roles()
    {
        return self::where('parent_id', '!=', null)->get();
    }

    public static function get_all_admin_role()
    {
        return self::where('name', '!=', "Super_admin")->get();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function users()
    {
        return $this->belongsToMany('App\User', null);
    }

    /**
     * A user belongs to some users of the model associated with its guard.
     */
    public function access_modules()
    {
        return $this->morphMany('Sagartakle\Laracrud\\AccessModule', 'assessor');
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
