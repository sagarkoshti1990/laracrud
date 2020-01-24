<?php

namespace Sagartakle\Laracrud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Actuallymab\LaravelComment\Commentable;

class Setting extends Model
{
    // use SoftDeletes;
    // use Commentable;

     /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'settings';
	
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
     * get module info of this Setting.
     *
     * @param module
     *
     * @return void
     */
    public Static function get_module()
    {
        return \Module::where('name', 'Setting')->first();
    }

    /**
     * get module info of this Setting.
     *
     * @param module
     *
     * @return void
     */
    public Static function value($key, $default = NULL)
    {
		if(\Schema::hasTable('menus')) {
            $value = self::where('key', $key)->first();
            $crud = app('Sagartakle\Laracrud\Controllers\ModulesController')->setting_crud;
            if(isset($value->value)) {
                return \FormBuilder::get_field_value($crud, 'value', $value->value, collect(config('lara.base.setting_keys'))->where('key',$value->key)->first()['type'],'value');
            } elseif(isset($default)) {
                return $default;
            } else {
                if($key == 'COMPANY_NAME') {
                    return config('lara.base.project_name');
                } elseif($key == 'COMPANY_LOGO') {
                    return asset('public/img/logo.png');
                }
            }    
        } elseif(isset($default)) {
            return $default;        
        } else {
            if($key == 'COMPANY_NAME') {
                return config('lara.base.project_name');
            } elseif($key == 'COMPANY_LOGO') {
                return asset('public/img/logo.png');
            }
        }
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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
