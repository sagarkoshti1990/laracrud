<?php

namespace Sagartakle\Laracrud\Models;

use Illuminate\Database\Eloquent\Model;

class FieldType extends Model
{

     /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'field_types';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

    public $timestamps = false;

	// protected $dates = ['deleted_at'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    // FieldType::getFTypes()
    public static function getFTypes()
    {
        $fields = config('stlc.field_type_model')::all();
        $fields2 = array();
        foreach($fields as $field) {
            $fields2[$field['name']] = $field['id'];
        }
        return $fields2;
    }
    
    // FieldType::getFTypes2()
    public static function getFTypes2()
    {
        $fields = config('stlc.field_type_model')::all();
        $fields2 = array();
        foreach($fields as $field) {
            $fields2[$field['id']] = $field['name'];
        }
        return $fields2;
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
