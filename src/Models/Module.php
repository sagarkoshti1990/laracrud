<?php

namespace Sagartakle\Laracrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Exception;
use Log;
use DB;

use Sagartakle\Laracrud\User;
use Sagartakle\Laracrud\Models\AccessModule;
// use Sagartakle\Laracrud\Models\Page;

class Module extends Model
{
    
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'modules';
	
	protected $hidden = [
        
    ];

    protected $guarded = [];
    
    public $timestamps = false;
    
	// protected $dates = [];
    
    /**
	* Get Module by module name
	* $module = self::make($module_name);
	**/
	public static function make($module_name,$data = []) {
		$module = null;
		if(is_int($module_name)) {
			$module = self::find($module_name);
		} else {
			$module = self::where('name', $module_name)->first();
        }
        
		if(isset($module)) {
            $crud = new \ObjectHelper;
            
            if(isset($data->setModel) || isset($data['setModel'])) {
                $crud->setModel(($data->setModel ?? $data['setModel']));
            }
            if(isset($data['allowAccess']) && count($data['allowAccess']) > 0) {
                $crud->allowAccess($data['allowAccess']);
            }
            if(isset($data['setColumnsOnly']) && count($data['setColumnsOnly']) > 0) {
                $crud->setColumns($module->fields,$data['setColumnsOnly']);
            }
            if(isset($data['setFieldsOnly']) && count($data['setFieldsOnly']) > 0) {
                $crud->setFields($module->fields,$data['setFieldsOnly']);
            }
            if(isset($data['removeFields']) && count($data['removeFields']) > 0) {
                foreach($data['removeFields'] as $field) {
                    unset($crud->fields[$field]);
                }
            }
            if(isset($data->route_prefix) || isset($data['route_prefix'])) {
                $crud->setRoute(($data->route_prefix ?? $data['route_prefix'] ?? "").'/'.$module->table_name);
            }
            if(isset($data->setRoute) || isset($data['setRoute'])) {
                $crud->setRoute(($data->setRoute ?? $data['setRoute'] ?? ""));
            }
            if(isset($data->setEntityNameStrings) || isset($data['setEntityNameStrings'])) {
                $crud->setEntityNameStrings(($data->setEntityNameStrings[0] ?? $data['setEntityNameStrings'][0] ?? ""),($data->setEntityNameStrings[0] ?? $data['setEntityNameStrings'][0] ?? ""));
            }
            if(isset($data->row) || isset($data['row'])) {
                $crud->row = ($data->row ?? $data['row'] ?? "");
            }
            
            if(isset($data->setViewPath) || isset($data['setViewPath'])) {
                $path = $data->setViewPath ?? $data['setViewPath'];
                if(is_string($path)) {
                    $crud->setViewPath([
                        'index'=> $path.'.index',
                        'create'=> ($path == 'crud') ? $path.'.form' : $path.'.create',
                        'edit'=> ($path == 'crud') ? $path.'.form' : $path.'.edit',
                        'show'=> $path.'.show',
                    ]);
                } else if(is_array($path)) {
                    $crud->setViewPath(($data->setViewPath ?? $data['setViewPath']));
                }
            }
            $crud->setModule($module);
            
            return $crud;
		} else {
			return null;
		}
    }
    
    public static function user()
    {
        $guards = array_keys(config('auth.guards',[]));
        foreach($guards as $guard) {
            if(\Auth::guard($guard)->check() && \Auth::guard($guard)->user()) {
                return \Auth::guard($guard)->user();
            }
        }
        return null;
    }

    /**
     * A user belongs to some users of the model associated with its guard.
     */
    public function accessible()
    {
        return $this->morphMany('Sagartakle\Laracrud\Models\AccessModule', 'accessible');
    }

    /**
     * Get the corresponding Eloquent Model for the CrudController, as defined with the setModel() function;.
     *
     * @return [Eloquent Collection]
     */
    public static function access_modules($accessible)
    {
        $modules = self::whereNotIn('name', ['Users','Tests','Uploads'])->get();
        if(Schema::hasTable('pages')) {
            $pages = Page::all();
        } else {
            $pages = [];
        }
        $modules_access = [];
        foreach ($modules as $module_obj) {
            $module_obj->accesses = $accessible->access_modules()
                    ->where([
                        ['accessible_id',$module_obj->id],
                        ['accessible_type', get_class($module_obj)]
                    ])->pluck('access');
            $module_obj->type = 'module';
            $modules_access[] = $module_obj;
        }
        foreach ($pages as $module_obj) {
            $module_obj->accesses = $accessible->access_modules()
                    ->where([
                        ['accessible_id',$module_obj->id],
                        ['accessible_type', get_class($module_obj)]
                    ])->pluck('access');
            $module_obj->type = 'page';
            $modules_access[] = $module_obj;
        }
        return $modules_access;
    }

    public static function custome_all_modules() {
        return self::whereNotIn('name', ['Users','Uploads','Permissions','Roles','Tests']);
    }
    /**
     * Get the corresponding Eloquent Model for the CrudController, as defined with the setModel() function;.
     *
     * @return [Eloquent Collection]
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the route for this CRUD.
     * Ex: admin/article.
     *
     * @param [string] Route name.
     * @param [array] Parameters.
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * This function handles Module Migration via "self::generate()" call from migrations file.
     * This creates all given Module fields into database.
     *
     * @param $module arrya[name,table,etc]
     * @param $module_table Module Database name in lowercase and concatenated by underscore.
     * @param $represent_attr View Column of Module for Index Anchor purpose.
     * @param string $faIcon Module FontAwesome Icon "fa-smile"
     * @param $fields Array of Module fields
     * @throws Exception Throws exceptions if Invalid attribute_name provided.
     */
    public static function generate($module_name, $table_name, $represent_attr, $icon = "fa fa-smile", $fields = [],$module = [])
    {
        $names = \CustomHelper::generateModuleNames($module_name);
        if(is_array($module) && count($module) > 0) {
            $names = array_merge($names,$module);
        }
        $names['name']=$module_name;$names['table_name'] = $table_name;$names['represent_attr']=$represent_attr;$names['icon']=$icon;
        $fields = self::format_fields($module_name, $fields);
        
        if(!self::validate_represent_attrumn($fields, $names['represent_attr'])) {
            throw new Exception("Unable to generate migration for " . ($names['name']) . " : attribute_name not found in field list.", 1);
        } else {
            // Check is Generated
            // $is_gen = false;
            // if(file_exists(base_path('app/Http/Controllers/' . ($names->controller) . ".php"))) {
            //     if(($names->model == "User" || $names->model == "Role" || $names->model == "Permission") && file_exists(base_path('app/' . ($names->model) . ".php"))) {
            //         $is_gen = true;
            //     } else if(file_exists(base_path('app/Models/' . ($names->model) . ".php"))) {
            //         $is_gen = true;
            //     }
            // }
            
            // Create Module if not exists
            if(Schema::hasTable('modules')) {
                $module = self::where('name', $names['name'])->first();
                if(!isset($module->id)) {
                    $module = self::create($names);
                }
            } else {
                $module = [];
            }
            if(Schema::hasTable('field_types')) {
                $ftypes = \FieldType::getFTypes();
            } else {
                $ftypes = [];
            }
            
            if(Schema::hasTable($table_name)) {
                Schema::table($table_name, function (Blueprint $table) use ($fields, $module, $ftypes) {
                    foreach($fields as $key => $field) {
                        if(Schema::hasTable('fields') && isset($module->id)) {
                            $mod = \Field::where('module_id', $module->id)->where('name', $field->name)->first();
                        }
                        if(isset($mod->id)) {
                            $field->id = $mod->id;
                            $field->module_obj = $module;

                            if(isset($field->nullable_required) && !$field->nullable_required) {
                                self::create_field_schema($table, $field, false, true);
                            } else {
                                self::create_field_schema($table, $field, true, true);
                            }
                        } else {
                            // Create Module field Metadata / Context
                            if(Schema::hasTable('fields')) {
                                $field_obj = \Field::create([
                                    'name' => $field->name,
                                    'label' => $field->label,
                                    'rank' => $field->rank ?? ($key * 5),
                                    'module_id' => $module->id,
                                    'field_type_id' => $ftypes[$field->field_type],
                                    'unique' => $field->unique,
                                    'defaultvalue' => $field->defaultvalue,
                                    'minlength' => $field->minlength,
                                    'maxlength' => $field->maxlength,
                                    'required' => $field->required,
                                    'nullable_required' => $field->nullable_required,
                                    'show_index' => $field->show_index,
                                    'json_values' => $field->json_values
                                ]);
                                $field->id = $field_obj->id;
                                $field->module_obj = $module;
                            }

                            // Create Module field schema in database
                            if(isset($field->nullable_required) && !$field->nullable_required) {
                                self::create_field_schema($table, $field, false);
                            } else {
                                self::create_field_schema($table, $field);
                            }
                        }
                    }
                });
            } else {
                // Create Database Schema for table
                Schema::create($table_name, function (Blueprint $table) use ($fields, $module, $ftypes,$table_name) {
                    $table->bigIncrements('id');
                    foreach($fields as $key => $field) {
                        if(Schema::hasTable('fields') && isset($module->id)) {
                            $mod = \Field::where('module_id', $module->id)->where('name', $field->name)->first();
                            if(!isset($mod->id)) {
                                // Create Module field Metadata / Context
                                $field_obj = \Field::create([
                                    'name' => $field->name,
                                    'label' => $field->label,
                                    'rank' => $field->rank ?? ($key * 5),
                                    'module_id' => $module->id,
                                    'field_type_id' => $ftypes[$field->field_type],
                                    'unique' => $field->unique,
                                    'defaultvalue' => $field->defaultvalue,
                                    'minlength' => $field->minlength,
                                    'maxlength' => $field->maxlength,
                                    'required' => $field->required,
                                    'nullable_required' => $field->nullable_required,
                                    'show_index' => $field->show_index,
                                    'json_values' => $field->json_values
                                ]);
                                $field->id = $field_obj->id;
                                $field->module_obj = $module;
                            }
                        }
                        
                        // Create Module field schema in database
                        if(isset($field->nullable_required) && !$field->nullable_required) {
                            self::create_field_schema($table, $field, false);
                        } else {
                            self::create_field_schema($table, $field);
                        }
                    }
                    if($table_name == "users") {
                        $table->rememberToken();
                    }
                    $table->softDeletes();
                    $table->timestamps();
                });
            }
        }
    }

    /**
     * Method creates database table field via $table variable from Schema
     * @param $table
     * @param $field
     * @param bool $update
     * @param bool $isFieldTypeChange
     */
    public static function create_field_schema($table, $field, $nullable_required = true, $update = false, $isFieldTypeChange = false)
    {
        if(is_numeric($field->field_type)) {
            $ftypes = \FieldType::getFTypes();
            $field->field_type = array_search($field->field_type, $ftypes);
        }
        if(!is_string($field->defaultvalue)) {
            $defval = json_encode($field->defaultvalue);
        } else {
            $defval = $field->defaultvalue;
        }
        // Log::debug('Module:create_field_schema ('.$update.') - '.$field->name." - ".$field->field_type
        // ." - ".$defval." - ".$field->maxlength);
        
        // Create Field in Database for respective Field Type
        switch($field->field_type) {
            case 'Address':
                $var = null;
                if($field->maxlength == 0) {
                    $var = $table->text($field->name);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Checkbox':
                $var = $table->string($field->name, 256);
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if(is_array($field->defaultvalue)) {
                    $field->defaultvalue = json_encode($field->defaultvalue);
                    $var->default($field->defaultvalue);
                } else if(is_string($field->defaultvalue) && \Str::startsWith($field->defaultvalue, "[")) {
                    $var->default($field->defaultvalue);
                } else if($field->defaultvalue == "" || $field->defaultvalue == null) {
                    $var->default("[]");
                } else if(is_string($field->defaultvalue)) {
                    $field->defaultvalue = json_encode([$field->defaultvalue]);
                    $var->default($field->defaultvalue);
                } else if(is_int($field->defaultvalue)) {
                    $field->defaultvalue = json_encode([$field->defaultvalue]);
                    //echo "int: ".$field->defaultvalue;
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("[]");
                }
                break;
            case 'Ckeditor':
                $var = null;
                if($field->maxlength == 0) {
                    $var = $table->text($field->name);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Hidden':
                if($field->json_values == "") {
                    if(is_int($field->defaultvalue)) {
                        if($update) {
                            $var = $table->unsignedBigInteger($field->name)->nullable()->change();
                        } else {
                            $var = $table->unsignedBigInteger($field->name)->nullable();
                        }
                        $var->default($field->defaultvalue);
                        break;
                    } else if(is_string($field->defaultvalue)) {
                        if($update) {
                            $var = $table->string($field->name)->nullable()->change();
                        } else {
                            $var = $table->string($field->name)->nullable();
                        }
                        $var->default($field->defaultvalue);
                        break;
                    } else {
                        $var = $table->string($field->name)->nullable();
                        $var->default($field->defaultvalue);
                        break;
                    }
                }
                $json_values = json_decode($field->json_values, true);
                if(\Str::startsWith($field->json_values, "@")) {
                    $foreign_table_name = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', str_replace("@", "", $field->json_values))), '_'));
                    if($update) {
                        $var = $table->unsignedBigInteger($field->name)->nullable()->change();
                        if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                            $var->default(NULL);
                        } else {
                            $var->default($field->defaultvalue);
                        }
                        $table->dropForeign($field->module_obj->table . "_" . $field->name . "_foreign");
                        $table->foreign($field->name)->references('id')->on($foreign_table_name)->onUpdate('cascade')->onDelete('cascade');
                    } else {
                        $var = $table->unsignedBigInteger($field->name)->nullable();
                        if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                            $var->default(NULL);
                        } else {
                            $var->default($field->defaultvalue);
                        }
                        $table->foreign($field->name)->references('id')->on($foreign_table_name)->onUpdate('cascade')->onDelete('cascade');
                    }
                } else if(is_array($json_values)) {
                    if($update) {
                        $var = $table->string($field->name)->nullable()->change();
                    } else {
                        $var = $table->string($field->name)->nullable();
                    }
                    if($field->defaultvalue != "") {
                        $var->default($field->defaultvalue);
                    } else if($field->required) {
                        $var->default("");
                    }
                } else if(is_object($json_values)) {
                    if($update) {
                        $var = $table->unsignedBigInteger($field->name)->nullable()->change();
                    } else {
                        $var = $table->unsignedBigInteger($field->name)->nullable();
                    }
                }
                break;
            case 'Currency':
                if($field->maxlength == 0) {
                    $var = $table->double($field->name, 15, 2);
                } else {
                    $var = $table->double($field->name, $field->maxlength, 2);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("0.0");
                }
                break;
            case 'Date':
            case 'Date_picker':
                $var = $table->date($field->name);
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                
                if($field->defaultvalue == NULL || $field->defaultvalue == "" || $field->defaultvalue == "NULL") {
                    $var->default(NULL);
                } else if($field->defaultvalue == "now()") {
                    $var->default(NULL);
                } else if($field->required) {
                    $var->default("1970-01-01");
                } else {
                    $var->default($field->defaultvalue);
                }
                break;
            case 'Datetime':
            case 'Datetime_picker':
                $var = $table->dateTime($field->name);
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                // $table->timestamp('created_at')->useCurrent();
                if($field->defaultvalue == NULL || $field->defaultvalue == "" || $field->defaultvalue == "NULL") {
                    $var->default(Null);
                } else if($field->defaultvalue == "now()") {
                    $var->default(DB::raw('CURRENT_TIMESTAMP'));
                } else if($field->required) {
                    $var->default("1970-01-01 01:01:01");
                } else {
                    $var->default($field->defaultvalue);
                }
                break;
            case 'Decimal':
                $var = $table->decimal($field->name, 38, 2);
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("0.0");
                }
                break;
            case 'Radio':
            case 'Select':
            case 'Select2':
            case 'Select2_from_ajax':
                $json_values = json_decode($field->json_values, true);
                if(\Str::startsWith($field->json_values, "@")) {
                    $foreign_table_name = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', str_replace("@", "", $field->json_values))), '_'));
                    if($update) {
                        $var = $table->unsignedBigInteger($field->name)->nullable()->change();
                        if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                            $var->default(NULL);
                        } else {
                            $var->default($field->defaultvalue);
                        }
                        $table->dropForeign($field->module_obj->table . "_" . $field->name . "_foreign");
                        $table->foreign($field->name)->references('id')->on($foreign_table_name)->onUpdate('cascade')->onDelete('cascade');
                    } else {
                        $var = $table->unsignedBigInteger($field->name)->nullable();
                        if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                            $var->default(NULL);
                        } else {
                            $var->default($field->defaultvalue);
                        }
                        $table->foreign($field->name)->references('id')->on($foreign_table_name)->onUpdate('cascade')->onDelete('cascade');
                    }
                } else if(is_array($json_values)) {
                    if($update) {
                        if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name)->change();
                            } else {
                                $var = $table->string($field->name)->nullable()->change();
                            }
                        }
                    } else {
                        if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name)->comment($field->json_values);
                            } else {
                                $var = $table->string($field->name)->nullable()->comment($field->json_values);
                            }
                        }
                    }
                    if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                        if(isset($field->defaultvalue) && $field->defaultvalue != "" && is_string($field->defaultvalue)) {
                            $var->default($field->defaultvalue);
                        } else if($field->required && isset($field->defaultvalue) && $field->defaultvalue != "") {
                            $var->default(Null);
                        }
                    } else {
                        if($field->defaultvalue != "") {
                            $var->default($field->defaultvalue);
                        } else if($field->required) {
                            $var->default(Null);
                        }
                    }
                } else if(is_object($json_values)) {
                    if($update) {
                        $var = $table->unsignedBigInteger($field->name)->nullable()->change();
                    } else {
                        $var = $table->unsignedBigInteger($field->name)->nullable();
                    }
                }
                break;
            case 'Select2_tags':
                $json_values = json_decode($field->json_values, true);
                if(isset($json_values) && is_array($json_values)) {
                    if($update) {
                        if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name)->change();
                            } else {
                                $var = $table->string($field->name)->nullable()->change();
                            }
                        }
                    } else {
                        if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name);
                            } else {
                                $var = $table->string($field->name)->nullable();
                            }
                        }
                    }
                    if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                        if(isset($field->defaultvalue) && $field->defaultvalue != "" && is_string($field->defaultvalue)) {
                            $var->default($field->defaultvalue);
                        } else if($field->required && isset($field->defaultvalue) && $field->defaultvalue != "") {
                            $var->default(Null);
                        }
                    } else {
                        if($field->defaultvalue != "") {
                            $var->default($field->defaultvalue);
                        } else if($field->required) {
                            $var->default(Null);
                        }
                    }
                }
                break;
            case 'Select2_multiple_tags': 
                $var = null;
                if($field->maxlength == 0) {
                    $var = $table->text($field->name);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Table': 
                $json_values = json_decode($field->json_values, true);
                if(isset($json_values) && is_array($json_values)) {
                    if($update) {
                        if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                            if($field->required && $nullable_required) {
                                $var = $table->text($field->name)->change();
                            } else {
                                $var = $table->text($field->name)->nullable()->change();
                            }
                        }
                    } else {
                        if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                            if($field->required && $nullable_required) {
                                $var = $table->text($field->name);
                            } else {
                                $var = $table->text($field->name)->nullable();
                            }
                        }
                    }
                    if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                        if(isset($field->defaultvalue) && $field->defaultvalue != "" && is_string($field->defaultvalue)) {
                            $var->default($field->defaultvalue);
                        } else if($field->required && isset($field->defaultvalue) && $field->defaultvalue != "") {
                            $var->default(Null);
                        }
                    } else {
                        if($field->defaultvalue != "") {
                            $var->default($field->defaultvalue);
                        } else if($field->required) {
                            $var->default(Null);
                        }
                    }
                }
            break;
            case 'Email':
                $var = null;
                if($field->maxlength == 0) {
                    $var = $table->string($field->name, 100);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Image':
            case 'File':
                if($update) {
                    $var = $table->unsignedBigInteger($field->name)->nullable()->change();
                    if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                        $var->default(NULL);
                    } else {
                        $var->default($field->defaultvalue);
                    }
                    $table->dropForeign($field->module_obj->table . "_" . $field->name . "_foreign");
                    $table->foreign($field->name)->references('id')->on(config('stlc.upload_table','uploads'))->onUpdate('cascade')->onDelete('cascade');
                } else {
                    $var = $table->unsignedBigInteger($field->name)->nullable();
                    if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                        $var->default(NULL);
                    } else {
                        $var->default($field->defaultvalue);
                    }
                    $table->foreign($field->name)->references('id')->on(config('stlc.upload_table','uploads'))->onUpdate('cascade')->onDelete('cascade');
                }
                break;
            case 'Files':
                break;
            case 'Float':
                if($field->maxlength == 0) {
                    $var = $table->float($field->name,8,2);
                } else {
                    $var = $table->float($field->name, $field->maxlength,2);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("0.0");
                }
                break;
            case 'Json':
                if($field->maxlength == 0) {
                    $var = $table->string($field->name, 256);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if(is_array($field->defaultvalue)) {
                    $field->defaultvalue = json_encode($field->defaultvalue);
                    $var->default($field->defaultvalue);
                } else if(is_string($field->defaultvalue) && \Str::startsWith($field->defaultvalue, "[")) {
                    $var->default($field->defaultvalue);
                } else if($field->defaultvalue == "" || $field->defaultvalue == null) {
                    $var->default("[]");
                } else if(is_string($field->defaultvalue)) {
                    $field->defaultvalue = json_encode([$field->defaultvalue]);
                    $var->default($field->defaultvalue);
                } else if(is_int($field->defaultvalue)) {
                    $field->defaultvalue = json_encode([$field->defaultvalue]);
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("[]");
                }
                break;
            case 'Number':
                $var = $table->integer($field->name, false);
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else {
                    $var->default(Null);
                }
                break;
            case 'Phone':
                if($field->maxlength == 0) {
                    $var = $table->string($field->name);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default(NULL);
                }
                break;
            case 'Polymorphic_select':
                $var = null;
                $var = $table->nullableMorphs($field->name);
                if($update) {
                    $var->change();
                }
                break;
            case 'Polymorphic_multiple':
                break;
            case 'Multiselect':
            case 'Select2_multiple':
                if($field->maxlength == 0) {
                    $var = $table->string($field->name,256);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if(is_array($field->defaultvalue)) {
                    $field->defaultvalue = json_encode($field->defaultvalue);
                    $var->default($field->defaultvalue);
                } else if(is_string($field->defaultvalue) && \Str::startsWith($field->defaultvalue, "[")) {
                    $var->default($field->defaultvalue);
                } else if($field->defaultvalue == "" || $field->defaultvalue == null) {
                    $var->default("[]");
                } else if(is_string($field->defaultvalue)) {
                    $field->defaultvalue = json_encode([$field->defaultvalue]);
                    $var->default($field->defaultvalue);
                } else if(is_int($field->defaultvalue)) {
                    $field->defaultvalue = json_encode([$field->defaultvalue]);
                    //echo "int: ".$field->defaultvalue;
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("[]");
                }
                break;
            case 'Password':
                $var = null;
                if($field->maxlength == 0) {
                    $var = $table->string($field->name);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Text':
                $var = null;
                if($field->maxlength == 0) {
                    $var = $table->string($field->name);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != null) {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Textarea':
                $var = null;
                if($field->maxlength == 0) {
                    $var = $table->text($field->name);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'URL':
                $var = null;
                if($field->maxlength == 0) {
                    $var = $table->string($field->name);
                } else {
                    $var = $table->string($field->name, $field->maxlength);
                }
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Month':
                $var = $table->date($field->name);
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                
                if($field->defaultvalue == NULL || $field->defaultvalue == "" || $field->defaultvalue == "NULL") {
                    $var->default(NULL);
                } else if($field->defaultvalue == "now()") {
                    $var->default(NULL);
                } else if($field->required) {
                    $var->default("1970-01");
                } else {
                    $var->default($field->defaultvalue);
                }
                break;
            case 'Time':
                $var = $table->time($field->name);
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $var->change();
                    } else {
                        $var = $var->nullable()->change();
                    }
                } else {
                    if(!($field->required && $nullable_required)) {
                        $var = $var->nullable();
                    }
                }
                // $table->timestamp('created_at')->useCurrent();
                if($field->defaultvalue == NULL || $field->defaultvalue == "" || $field->defaultvalue == "NULL") {
                    $var->default(Null);
                } else if($field->defaultvalue == "now()") {
                    $var->default(DB::raw('CURRENT_TIMESTAMP'));
                } else if($field->required) {
                    $var->default("01:01:01");
                } else {
                    $var->default($field->defaultvalue);
                }
                break;
            default:
                $var = $table->string($field->name, $field->maxlength)->nullable();
                $var->default(NULL);
        }
        
        // set column unique
        if($update) {
            if($isFieldTypeChange) {
                if($field->unique && $var != null && $field->maxlength < 256) {
                    $table->unique($field->name);
                }
            }
        } else {
            if($field->unique && $var != null && $field->maxlength < 256) {
                $table->unique($field->name);
            }
        }
    }

    /**
     * Validates if given view_column_name exists in fields array
     *
     * @param $fields Array of fields from migration file
     * @param $view_col View Column Name
     * @return bool returns true if view_column_name found in fields otherwise false
     */
    public static function validate_represent_attrumn($fields, $view_col)
    {
        $found = false;
        foreach($fields as $field) {
            if($field->name == $view_col) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    /**
     * This method process and alters user created migration fields array to fit into standard field Context / Metedata
     *
     * Note: field array type change
     * Earlier we were taking sequential array for fields, but from version 1.1 we are using different format
     * with associative array. It also supports old sequential array. This step is taken to accommodate "show_index"
     * which allows field to be listed in index/listing table. This step will also allow us to take more Metadata about
     * field.
     *
     * @param $module_name Module Name
     * @param $fields Fields Array
     * @return array Returns Array of Field Objects
     * @throws Exception Throws exception if field missing any details like name, label, field_type
     */
    public static function format_fields($module_name, $fields)
    {
        $out = [];
        foreach($fields as $field) {
            // Check if field format is New
            if(\CustomHelper::is_assoc_array($field)) {
                $obj = (object)$field;
                
                if(!isset($obj->name)) {
                    throw new Exception("Migration " . $module_name . " -  Field does not have name", 1);
                } else if(!isset($obj->label)) {
                    throw new Exception("Migration " . $module_name . " -  Field does not have label", 1);
                } else if(!isset($obj->field_type)) {
                    throw new Exception("Migration " . $module_name . " -  Field does not have field_type", 1);
                }
                if(!isset($obj->unique)) {
                    $obj->unique = 0;
                }
                if(!isset($obj->defaultvalue)) {
                    $obj->defaultvalue = '';
                }
                if(!isset($obj->minlength)) {
                    $obj->minlength = 0;
                }
                if(!isset($obj->maxlength)) {
                    $obj->maxlength = 0;
                } else {
                    // Because maxlength above 256 will not be supported by Unique
                    if($obj->unique && !isset($obj->maxlength)) {
                        $obj->maxlength = 250;
                    } else {
                        $obj->maxlength = $obj->maxlength;
                    }
                }
                if(!isset($obj->required)) {
                    $obj->required = 0;
                }
                if(isset($obj->nullable_required) && $obj->nullable_required == false) {
                    $obj->nullable_required = false;
                } else {
                    $obj->nullable_required = true;
                }
                if(!isset($obj->show_index)) {
                    $obj->show_index = 0;
                } else {
                    if($obj->show_index == true) {
                        $obj->show_index = 1;
                    } else {
                        $obj->show_index = 0;
                    }
                }
                
                if(!isset($obj->json_values)) {
                    $obj->json_values = "";
                } else {
                    if(is_array($obj->json_values)) {
                        $obj->json_values = json_encode($obj->json_values);
                    } else {
                        $obj->json_values = $obj->json_values;
                    }
                }
                // var_dump($obj);
                $out[] = $obj;
            } else {
                // Handle Old field format - Sequential Array
                $obj = (Object)[];
                $obj->name = $field[0];
                $obj->label = $field[1];
                $obj->field_type = $field[2];
                
                if(!isset($field[3])) {
                    $obj->unique = 0;
                } else {
                    $obj->unique = $field[3];
                }
                if(!isset($field[4])) {
                    $obj->defaultvalue = '';
                } else {
                    $obj->defaultvalue = $field[4];
                }
                if(!isset($field[5])) {
                    $obj->minlength = 0;
                } else {
                    $obj->minlength = $field[5];
                }
                if(!isset($field[6])) {
                    $obj->maxlength = 0;
                } else {
                    // Because maxlength above 256 will not be supported by Unique
                    if($obj->unique) {
                        $obj->maxlength = 250;
                    } else {
                        $obj->maxlength = $field[6];
                    }
                }
                if(!isset($field[7])) {
                    $obj->required = 0;
                } else {
                    $obj->required = $field[7];
                }
                $obj->show_index = 1;
                
                if(!isset($field[8])) {
                    $obj->json_values = "";
                } else {
                    if(is_array($field[8])) {
                        $obj->json_values = json_encode($field[8]);
                    } else {
                        $obj->json_values = $field[8];
                    }
                }
                $out[] = $obj;
            }
        }
        return $out;
    }
    
    /**
     * Create Validations rules array for Laravel Validations using Module Field Context / Metadata
     * 
     * This generates array of validation rules for whole Module
     *
     * @param $module_name Module Name
     * @param $request \Illuminate\Http\Request Object
     * @param bool $isEdit Is this a Update or Store Request
     * @return array Returns Array to validate given Request
     */
    public static function validateRules($crud, $request, $isEdit = false,$segment = null)
    {
        if(!isset($crud->module->id)) {
            $crud = self::make($crud);
        }
        
        $rules = [];
        if(isset($crud->module->id)) {
            $ftypes = \FieldType::getFTypes2();
            $add_from = true;

            foreach($crud->fields as $field) {
                if($isEdit && !(isset($request->{$field['name']}) || isset($request[$field['name']])) ) {
                    $add_from = false;
                } else {
                    $add_from = true;
                }
                if($add_from) {
                    $col = "";
                    if($field['required']) {
                        if(in_array($ftypes[$field['field_type']["id"]], ["Json"]) && isset($field->json_values) && is_array(json_decode($field->json_values))) {
                            foreach(json_decode($field->json_values) as $json_values_f) {
                                $rules[$field['name'].'_'.$json_values_f] = 'required';
                            }
                        } else {
                            $col .= "required|";
                        }
                    } else {
                        $col .= "nullable|";
                    }
                    if(in_array($ftypes[$field['field_type']["id"]], ["Currency", "Decimal"])) {
                    
                        // No min + max length
                    } else {
                        if($field['minlength'] != 0) {
                            $col .= "min:" . $field['minlength'] . "|";
                        }
                        if($field['maxlength'] != 0) {
                            $col .= "max:" . $field['maxlength'] . "|";
                        }
                    }
                    if(in_array($ftypes[$field['field_type']["id"]], ['Datetime_picker'])) {
                        $col .= 'date|date_format:"Y-m-d H:i:s"';
                    }
                    if(in_array($ftypes[$field['field_type']["id"]], ['Date_picker'])) {
                        $col .= 'date|date_format:"Y-m-d"';
                    }

                    if(in_array($ftypes[$field['field_type']["id"]], ["Email"])) {
                        $col .= "regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/|";
                    }
                    if($field['unique'] && !$isEdit) {
                        $filed_unique_value = isset($request->{$field['name']})? ','.$request->{$field['name']}:"";
                        $col .= "unique:" . $crud->table_name.','.$field['name'].$filed_unique_value;
                    } else if($isEdit && $field['unique']) {
                        $col .= "unique:" . $crud->table_name.','.$field['name'].','.($segment ? $segment : (!is_array($request) ? $request->segment(3) : ""));
                    }
                    if(\Str::startsWith($field->json_values, "@") && (isset($request->{$field['name']}) || (is_array($request) && isset($request[$field['name']])))) {
                        $foreign_table_name = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', str_replace("@", "", $field->json_values))), '_'));
                        $col .= "exists:" . $foreign_table_name .',id';
                    } else if(isset($field->json_values) && is_array($json_arrya = json_decode($field->json_values)) && count($json_arrya) > 0 && !in_array($ftypes[$field['field_type']["id"]], ["Table",'Select2_tags','Select2_multiple_tags'])) {
                        $col .= "in:" . implode(',',$json_arrya);
                    }
                    
                    if(in_array($ftypes[$field['field_type']["id"]], ['Date','Datetime'])) {
                        $col .= "date";
                    }
                    if($col != "") {
                        if(in_array($ftypes[$field['field_type']["id"]], ['Checkbox','Multiselect','Select2_multiple','Select2_multiple_tags','Table'])) {
                            if($field['required']) {
                                $rules[$field['name']] = 'required|array';
                            } else {
                                $rules[$field['name']] = 'nullable|array';
                            }
                            if(isset($field->json_values) && is_array($json_arrya = json_decode($field->json_values)) && count($json_arrya) > 0 && in_array($ftypes[$field['field_type']["id"]], ["Table"])) {
                                foreach($json_arrya as $json_arr) {
                                    $rules[$field['name'].'.*.'.$json_arr] = trim($col, "|");
                                }
                            } else {
                                $rules[$field['name'].'.*'] = trim($col, "|");
                            }
                        } else {
                            $rules[$field['name']] = trim($col, "|");
                        }
                    }
                    if($ftypes[$field['field_type']["id"]] == 'Polymorphic_select') {
                        unset($rules[$field['name']]);
                        $req = "";
                        if($field['required']) {
                            $req .= "required|";
                        } else {
                            $req .= "nullable|";
                        }
                        $ps_type = $field['name'].'_type';
                        $ps_id = $field['name'].'_id';
                        $rules[$ps_type] = trim(($req)."exists:modules,model", "|");
                        if(isset($request[$ps_type]) && class_exists($request[$ps_type])) {
                            $object = (new $request[$ps_type])->getTable();
                            $rules[$ps_id] = trim(($req)."exists:".$object.",id", "|");
                        }
                    }
                    if($ftypes[$field['field_type']["id"]] == 'Polymorphic_multiple') {
                        if($field['required']) {
                            $req = "required|";
                        } else {
                            $req = "nullable|";
                        }
                        $json_values = $field->json_values;
                        if(isset($json_values) && !empty($json_values) && is_string($json_values) && \Str::startsWith($json_values, "@")) {
                            $pm_module = \Module::where('name', str_replace("@", "", $json_values))->first();
                            if(isset($pm_module->represent_attr) && isset(($pm_field = $pm_module->fields->firstWhere('name',$pm_module->represent_attr))->json_values)) {
                                $json_values = $pm_field->json_values;
                                if(isset($json_values) && !empty($json_values) && is_string($json_values) && \Str::startsWith($json_values, "@")) {
                                    $pm_module = \Module::where('name', str_replace("@", "", $json_values))->first();
                                }
                                $req .= "exists:" . $pm_module->table_name .',id';
                            }
                        }
                        $rules[$field['name']] = trim($req, "|");
                    }
                }
            }
            
            // echo "<pre>";
            // echo json_encode($rules, JSON_PRETTY_PRINT);
            // echo "</pre>";
            // exit;
            return $rules;
        } else {
            return $rules;
        }
    }

    /**
     * Get Specific Module Access for login user or specific user ($user_id)
     *
     * self::hasAccess($module_id, $permission, $user_id);
     *
     * @param $module_id Module ID / Name
     * @param string $permission Access Type - view / create / edit / delete
     * @param int $user_id User id for which Access will be checked
     * @return bool Returns true if access is there or false
     */
    public static function hasRoleAccess($crud, $permission = "view", $role_id = false)
    {
        if(\Module::user() == null) {
            return false;
        }
        if(\Module::user()->isSuperAdmin()) {
            return true;
        }

        if($crud instanceof Page) {
            $module = $crud;
        } else if(is_string($crud) && !isset($crud->module->id)) {
            $module = self::where('name',$crud)->first();
        } else if(isset($crud->module->id)) {
            $module = $crud->module;
        }
        
        if($role_id) {
            $role = config('stlc.role_model')::find($role_id);
            if(isset($role->id)) {
                $roles = $role->roles();
            }
        } else if(\Module::user() != null){
            $roles = \Module::user()->roles();
        }
        if(isset($roles)) {
            foreach($roles->get() as $role) {
                if(isset($module->id)) {
                    $access = $role->access_modules()->where([
                        ['accessible_id', $module->id],
                        ['accessible_type', get_class($module)],
                        ['access', $permission]
                    ])->count();
                }
                if(isset($access) && $access > 0) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Get Module Access for all roles or specific role
     *
     * $role_accesses = self::getRoleAccess($id);
     *
     * @param $module_id Module ID
     * @param int $specific_role Specific role id
     * @return array Array of Roles with accesses
     */
    public static function getRoleAccess($module_id, $specific_role = 0)
    {
        $module = self::find($module_id);
        $module = self::make($module->name);
        
        if($specific_role) {
            $roles_arr = config('stlc.role_model')::where('id', $specific_role)->get();
        } else {
            $roles_arr = config('stlc.role_model')::all();
        }
        $roles = [];
        $arr_field_access = [
            'invisible' => 0,
            'readonly' => 1,
            'write' => 2
        ];
        
        foreach($roles_arr as $role) {
            // get Current Module permissions for this role
            
            $module_perm = DB::table('role_modules')->where('role_id', $role->id)->where('module_id', $module->module->id)->first();
            if(isset($module_perm->id)) {
                // set db values
                $role->view = $module_perm->view;
                $role->create = $module_perm->create;
                $role->edit = $module_perm->edit;
                $role->deactivate = $module_perm->deactivate;
            } else {
                $role->view = false;
                $role->create = false;
                $role->edit = false;
                $role->deactivate = false;
            }

            // get Current Module Fields permissions for this role
            
            $role->fields = [];
            foreach($module->module->fields as $field) {
                // find role field permission
                // $field_perm = DB::table('role_module_fields')->where('role_id', $role->id)->where('field_id', $field['id'])->first();
                
                // if(isset($field_perm->id)) {
                //     $field['access'] = $arr_field_access[$field_perm->access];
                // } else {
                //     $field['access'] = 0;
                // }
                // $role->fields[$field['id']] = $field;
                //$role->fields[$field['id']] = $field_perm->access;
            }
            $roles[] = $role;
        }
        return collect($roles);
    }

    /**
     * Get Specific Module Access for login user or specific user ($user_id)
     *
     * self::hasAccess($module_id, $permission, $user_id);
     *
     * @param $module_id Module ID / Name
     * @param string $permission Access Type - view / create / edit / delete
     * @param int $user_id User id for which Access will be checked
     * @return bool Returns true if access is there or false
     */
    public static function hasAccess($crud, $permission = "view", $user_id = false)
    {
        if(\Module::user() == null) {
            return false;
        }
        if(\Module::user()->isSuperAdmin() || self::hasRoleAccess($crud, $permission)) {
            return true;
        }

        $users = [];
        if($crud instanceof Page) {
            $module = $crud;
        } else if(is_string($crud) && !isset($crud->module->id)) {
            $module = self::where('name',$crud)->first();
        } else if(isset($crud->module->id)) {
            $module = $crud->module;
        }
        
        if($user_id) {
            $user = config('stlc.user_model')::find($user_id);
            if(isset($user->id)) {
                $user = $user;
            } else {
                $user = [];
            }
        } else {
            $user = \Module::user();
        }
        
        if(isset($user->id) && isset($module->id)) {
            $access = $user->access_modules()->where([
                ['accessible_id', $module->id],
                ['accessible_type', get_class($module)],
                ['access', $permission]
            ])->count();
            if(isset($access) && $access > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Module Access for all users or specific user
     *
     * $user_accesses = self::getUserAccess($id);
     *
     * @param $module_id Module ID
     * @param int $specific_user Specific user id
     * @return array Array of Users with accesses
     */
    public static function getUserAccess($module_id, $specific_user = 0)
    {
        $module = self::find($module_id);
        $module = self::make($module->name);
        
        if($specific_user) {
            $users_arr = config('stlc.user_model')::where('id', $specific_user)->get();
        } else {
            $users_arr = config('stlc.user_model')::all();
        }
        $users = [];
        
        $arr_field_access = [
            'invisible' => 0,
            'readonly' => 1,
            'write' => 2
        ];
        
        foreach($users_arr as $user) {
            // get Current Module permissions for this user
            
            $module_perm = DB::table('user_modules')->where('user_id', $user->id)->where('module_id', $module->module->id)->first();
            if(isset($module_perm->id)) {
                // set db values
                $user->view = $module_perm->view;
                $user->create = $module_perm->create;
                $user->edit = $module_perm->edit;
                $user->deactivate = $module_perm->deactivate;
            } else {
                $user->view = false;
                $user->create = false;
                $user->edit = false;
                $user->deactivate = false;
            }

            $users[] = $user;
        }
        return collect($users);
    }

    /**
     * Get list of Columns to display in Index Page for a particular Module
     * Also Filters the columns for Access control
     *
     * self::getListingColumns('Employees')
     *
     * @param $module_id_name Module Name / ID
     * @param bool $isObjects Whether you want just Names of Columns or Column Field Objects
     * @return array Array of Columns Names/Objects
     */
    public static function getListingColumns($module_id_name, $isObjects = false)
    {
        $module = null;
        if(is_int($module_id_name)) {
            $module = self::make($module_id_name)->module;
            $show_indexs = \Field::where('module_id', $module->id)->where('show_index', 1)->get()->toArray();
        } else if(is_string($module_id_name)) {
            $module = self::where('name', $module_id_name)->first();
            $show_indexs = \Field::where('module_id', $module->id)->where('show_index', 1)->get()->toArray();
        } else if(isset($module_id_name->module)){
            $module = $module_id_name->module;
            $show_indexs = collect($module_id_name->fields)->where('show_index', 1)->toArray();
        }
        
        if($isObjects) {
            $id_col = ['label' => $module->table_name.'.id', 'name' => $module->table_name.'.id'];
        } else {
            $id_col = $module->table_name.'.id';
        }
        $show_indexs_temp = [$id_col];
        foreach($show_indexs as $col) {
            // if(self::hasFieldAccess($module->id, $col['id'])) {
                if($isObjects) {
                    if(isset($col['field_type']['name']) && in_array($col['field_type']['name'], ['Polymorphic_multiple','Files'])) {
                        continue;
                    } else if(isset($col['field_type']['name']) && $col['field_type']['name'] == 'Polymorphic_select' && $module->fields->contains('name',$col->name)) {
                        $show_indexs_temp[] = $module->table_name.'.'.$col->name.'_id';
                    } else if($module->fields->contains('name',$col->name)) {
                        $show_indexs_temp[] = $module->table_name.'.'.$col->name;
                    } else {
                        $show_indexs_temp[] = $col;
                    }
                } else {
                    if(isset($col['field_type']['name']) && in_array($col['field_type']['name'], ['Polymorphic_multiple','Files'])) {
                        $show_indexs_temp[] = $module->table_name.'.id as '.$col['name'];
                    } else if(isset($col['field_type']['name']) && $col['field_type']['name'] == 'Polymorphic_select' && $module->fields->contains('name',$col['name'])) {
                        $show_indexs_temp[] = $module->table_name.'.'.$col['name'].'_id';
                    } else if($module->fields->contains('name',$col['name'])) {
                        $show_indexs_temp[] = $module->table_name.'.'.$col['name'];
                    } else {
                        $show_indexs_temp[] = $col['name'];
                    }
                }
            // }
        }
        
        return $show_indexs_temp;
    }
    
    /**
     * Set Default Access for given Module
     * Helps to set Full Module Access for Super Admin
     *
     * self::setDefaultAccess($module_id, $accessible);
     *
     * @param $module_id Module ID
     * @param $accessible object of model
     * @param string $permission Access Type - full / readonly
     */
    public static function setDefaultAccess($accessible, $assessor, $permission = "readonly", $deactivate = true)
    {
        $access_arr = [];
        if($permission == "full") {
            $access_arr = ['view','create','edit'];
            if($deactivate) {
                $access_arr[] = 'deactivate';
            }
        } else if($permission == "readonly") {
            $access_arr[] = 'view';
        }
        // 1. Set Module Access
        foreach($access_arr as $access) {
            config('stlc.access_module_model')::withTrashed()->updateOrCreate([
                'assessor_id' => $assessor->id,
                'assessor_type' => get_class($assessor),
                'accessible_id' => $accessible->id,
                'accessible_type' => get_class($accessible),
                'access' => $access
            ],[
                'deleted_at' => NULL
            ]);
        }
    }

    /**
     * Get the fields of this module.
     */
    public function get_field($name)
    {
        return $this->fields->where('name',$name)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get the fields of this module.
     */
    public function fields()
    {
        return $this->hasMany(\Field::class, 'module_id', 'id')->orderBy('rank');
    }
    /**
     * Get the fields of this module.
     * \Module::field_names_array($name)
     */
    public static function field_names_array($name = null)
    {
        return self::select('fields.name as name')->where('modules.name',($name))
                ->join('fields','fields.module_id','=','modules.id')->pluck('name');
    }

    public function dependency_fields()
    {
        return \Field::where('json_values','@'.$this->name)->with(['module','field_type'])->get();
    }

    public function delete_dependency($item_id)
    {
        $fields = $this->dependency_fields();
        $data = [];
        foreach($fields as $key => $field) {
            if(isset($field->field_type->name) && !in_array($field->field_type->name,['Polymorphic_multiple'])) {
                $items = $field->module->model::where($field->name,$item_id)->withTrashed()->get();
                if($items->count() > 0) {
                    $data[$key] = ["key" => $field->module->label,"value" => $items->count()];
                }
            }
        }
        return collect($data)->values();
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
