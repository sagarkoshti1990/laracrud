<?php

namespace Sagartakle\Laracrud\Models;

use Illuminate\Database\Eloquent\Model;
use Sagartakle\Laracrud\Models\FieldType;
use DB;
use Sagartakle\Laracrud\Models\Module;

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
        return $this->belongsTo('Sagartakle\Laracrud\Models\Module', 'module_id', 'id');
    }

    /**
     * Get the field_type of this field.
     */
    public function field_type()
    {
        return $this->belongsTo('Sagartakle\Laracrud\Models\FieldType', 'field_type_id', 'id');
    }
    
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getJsonModule()
    {
        if(isset($this->field_type_id) && $this->field_type->name == "Files") {
            return Module::where('name',"Uploadables")->first();
        } else {
            return Module::where('name', str_replace("@", "", $this->json_values))->first();
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
        $module = Module::where('name', $moduleName)->first();
        $fields = \DB::table('fields')->where('module_id', $module->id)->get();
        $ftypes = FieldType::getFTypes();
        
        $fields_popup = array();
        $fields_popup['id'] = null;
        
        // Set field type (e.g. Dropdown/Taginput) in String Format to field Object
        foreach($fields as $f) {
            $f->field_type_str = array_search($f->field_type_id, $ftypes);
            $fields_popup [$f->name] = $f;
        }
        return $fields_popup;
    }
    
    /**
     * Get Field Value when its associated with another Module / Table via "@"
     * e.g. "@employees"
     *
     * @param $field Module Field Object
     * @param $value_id This is a ID for which we wanted the Value from another table
     * @return mixed Returns Value found in table or Value id itself
     */
    public static function getFieldValue($field, $value_id)
    {
        $external_table_name = substr($field->json_values, 1);
        $module = Module::where('name', $external_table_name)->first();
        if(\Schema::hasTable($module->table_name)) {
            $external_value = DB::table($module->table_name)->where('id', $value_id)->first();
            if(isset($external_value->id)) {
                $external_module = $module;
                if(isset($external_module->represent_attr)) {
                    if(in_array($external_table_name, ["Employees","Leads","Cilets"])) {
                            if(isset($external_value->first_name)) {
                                return $external_value->first_name." ".$external_value->last_name;
                            } else {
                                return $external_value->last_name;
                            }
                    } else {
                        $represent_attr = $external_module->represent_attr;
                        return $external_value->$represent_attr;
                    }
                } else {
                    if(isset($external_value->name)) {
                        return $external_value->name;
                    } else if(isset($external_value->title)) {
                        return $external_value->title;
                    }
                }
            } else {
                return $value_id;
            }
        } else {
            return $value_id;
        }
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