<?php

namespace Sagartakle\Laracrud\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Field extends Model
{

     /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'fields';
	
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

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Get the module of this field.
     */
    public function module()
    {
        return $this->belongsTo(\Module::class, 'module_id', 'id');
    }

    /**
     * Get the field_type of this field.
     */
    public function field_type()
    {
        return $this->belongsTo(\FieldType::class, 'field_type_id', 'id');
    }
    
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getJsonModule()
    {
        if(isset($this->field_type_id) && $this->field_type->name == "Files") {
            return \Module::where('name',"Uploadables")->first();
        } else {
            return \Module::where('name', str_replace("@", "", $this->json_values))->first();
        }
    }

    /**
     * Get Array of Fields for given Module
     *
     * @param $moduleName Module Name
     * @return array Array of Field Objects
     */
    public static function getFields($moduleName)
    {
        $module = \Module::where('name', $moduleName)->first();
        $fields = \DB::table('fields')->where('module_id', $module->id)->get();
        $ftypes = \FieldType::getFTypes();
        
        $fields_popup = array();
        $fields_popup['id'] = null;
        
        // Set field type (e.g. Dropdown/Taginput) in String Format to field Object
        foreach($fields as $f) {
            $f->field_type_str = array_search($f->field_type_id, $ftypes);
            $fields_popup [$f->name] = $f;
        }
        return $fields_popup;
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