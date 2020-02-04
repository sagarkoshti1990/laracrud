<?php

namespace Sagartakle\Laracrud\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Exception;
use Log;
use DB;

use Sagartakle\Laracrud\Models\Field;
use Sagartakle\Laracrud\Models\FieldType;
use Sagartakle\Laracrud\Helpers\ObjectHelper;
use Sagartakle\Laracrud\Helpers\CustomHelper;
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
	public static function make($module_name) {
		$module = null;
		if(is_int($module_name)) {
			$module = self::find($module_name);
		} else {
			$module = self::where('name', $module_name)->first();
		}
		
		if(isset($module)) {
            $crud = new ObjectHelper;
            $crud->setModule($module);
            return $crud;
		} else {
			return null;
		}
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
        $pages = [];//Page::all();
        $modules_access = array();
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
     * @param $module_name Module Name
     * @param $module_table Module Database name in lowercase and concatenated by underscore.
     * @param $represent_attr View Column of Module for Index Anchor purpose.
     * @param string $faIcon Module FontAwesome Icon "fa-smile-o"
     * @param $fields Array of Module fields
     * @throws Exception Throws exceptions if Invalid represent_attrumn_name provided.
     */
    public static function generate($module_name, $table_name, $represent_attr, $faIcon = "fa-smile-o", $fields)
    {
        
        $names = CustomHelper::generateModuleNames($module_name, $faIcon);
        $fields = self::format_fields($module_name, $fields);
        
        if(substr_count($represent_attr, " ") || substr_count($represent_attr, ".")) {
            throw new Exception("Unable to generate migration for " . ($names->module) . " : Invalid represent_attrumn_name. 'This should be database friendly lowercase name.'", 1);
        } else if(!self::validate_represent_attrumn($fields, $represent_attr)) {
            throw new Exception("Unable to generate migration for " . ($names->module) . " : represent_attrumn_name not found in field list.", 1);
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
                $module = self::where('name', $names->module)->first();
                if(!isset($module->id)) {
                    $module = new self;
                    $module->name = $names->module;
                    $module->label = $names->label;
                    $module->table_name = $table_name;
                    $module->represent_attr = $represent_attr;
                    $module->model = $names->model;
                    $module->controller = $names->controller;
                    $module->icon = $faIcon;
                    $module->save();
                }
            } else {
                $module = [];
            }
            if(Schema::hasTable('field_types')) {
                $ftypes = FieldType::getFTypes();
                //print_r($ftypes);
                //print_r($module);
                //print_r($fields);
            } else {
                $ftypes = [];
            }
            
            if(Schema::hasTable($table_name)) {
                Schema::table($table_name, function (Blueprint $table) use ($fields, $module, $ftypes) {
                    foreach($fields as $field) {
                        if(Schema::hasTable('fields') && isset($module->id)) {
                            $mod = Field::where('module_id', $module->id)->where('name', $field->name)->first();
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
                                $field_obj = Field::create([
                                    'name' => $field->name,
                                    'label' => $field->label,
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
                    $table->increments('id');
                    foreach($fields as $field) {
                        if(Schema::hasTable('fields') && isset($module->id)) {
                            $mod = Field::where('module_id', $module->id)->where('name', $field->name)->first();
                            if(!isset($mod->id)) {
                                // Create Module field Metadata / Context
                                $field_obj = Field::create([
                                    'name' => $field->name,
                                    'label' => $field->label,
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
                    
                    // $table->string('name');
                    // $table->string('designation', 100);
                    // $table->string('mobile', 20);
                    // $table->string('mobile2', 20);
                    // $table->string('email', 100)->unique();
                    // $table->string('gender')->default('male');
                    // $table->integer('dept')->unsigned();
                    // $table->integer('role')->unsigned();
                    // $table->string('city', 50);
                    // $table->string('address', 1000);
                    // $table->string('about', 1000);
                    // $table->date('date_birth');
                    // $table->date('date_hire');
                    // $table->date('date_left');
                    // $table->double('salary_cur');
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
            $ftypes = FieldType::getFTypes();
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
            // case 'Address':
            //     $var = null;
            //     if($field->maxlength == 0) {
            //         if($update) {
            //             $var = $table->text($field->name)->change();
            //         } else {
            //             $var = $table->text($field->name);
            //         }
            //     } else {
            //         if($update) {
            //             $var = $table->string($field->name, $field->maxlength)->nullable()->change();
            //         } else {
            //             $var = $table->string($field->name, $field->maxlength)->nullable();
            //         }
            //     }
            //     if($field->defaultvalue != "") {
            //         $var->default($field->defaultvalue);
            //     } else if($field->required) {
            //         $var->default("");
            //     }
            //     break;
            case 'Checkbox':
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->string($field->name, 256)->change();
                    } else {
                        $var = $table->string($field->name, 256)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->string($field->name, 256);
                    } else {
                        $var = $table->string($field->name, 256)->nullable();
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
            case 'CKEditor':
                $var = null;
                if($field->maxlength == 0) {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->text($field->name)->change();
                        } else {
                            $var = $table->text($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->text($field->name);
                        } else {
                            $var = $table->text($field->name)->nullable();
                        }
                    }
                } else {
                    if($update) {
                        $var = $table->string($field->name, $field->maxlength)->nullable()->change();
                    } else {
                        $var = $table->string($field->name, $field->maxlength)->nullable();
                    }
                    if($field->defaultvalue != "") {
                        $var->default($field->defaultvalue);
                    } else if($field->required) {
                        $var->default("");
                    }
                }
                break;
            case 'Hidden':
                if($field->json_values == "") {
                    if(is_int($field->defaultvalue)) {
                        if($update) {
                            $var = $table->integer($field->name)->unsigned()->nullable()->change();
                        } else {
                            $var = $table->integer($field->name)->unsigned()->nullable();
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
                        $var = $table->integer($field->name)->nullable()->unsigned()->change();
                        if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                            $var->default(NULL);
                        } else {
                            $var->default($field->defaultvalue);
                        }
                        $table->dropForeign($field->module_obj->table . "_" . $field->name . "_foreign");
                        $table->foreign($field->name)->references('id')->on($foreign_table_name)->onUpdate('cascade')->onDelete('cascade');
                    } else {
                        $var = $table->integer($field->name)->nullable()->unsigned();
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
                    // ############### Remaining
                    if($update) {
                        $var = $table->integer($field->name)->nullable()->unsigned()->change();
                    } else {
                        $var = $table->integer($field->name)->nullable()->unsigned();
                    }
                    // if(is_int($field->defaultvalue)) {
                    //     $var->default($field->defaultvalue);
                    //     break;
                    // }
                }
                break;
            case 'Currency':
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->double($field->name, 15, 2)->change();
                    } else {
                        $var = $table->double($field->name, 15, 2)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->double($field->name, 15, 2);
                    } else {
                        $var = $table->double($field->name, 15, 2)->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("0.0");
                }
                break;
            case 'Date':
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->date($field->name)->change();
                    } else {
                        $var = $table->date($field->name)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->date($field->name);
                    } else {
                        $var = $table->date($field->name)->nullable();
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
                if($update) {
                    // Timestamp Edit Not working - http://stackoverflow.com/questions/34774628/how-do-i-make-doctrine-support-timestamp-columns
                    // Error Unknown column type "timestamp" requested. Any Doctrine type that you use has to be registered with \Doctrine\DBAL\Types\Type::addType()
                    // $var = $table->timestamp($field->name)->change();
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->timestamp($field->name)->nullableTimestamps();
                    } else {
                        $var = $table->timestamp($field->name)->nullable()->nullableTimestamps();
                    }
                }
                // $table->timestamp('created_at')->useCurrent();
                if($field->defaultvalue == NULL || $field->defaultvalue == "" || $field->defaultvalue == "NULL") {
                    $var->default(NULL);
                } else if($field->defaultvalue == "now()") {
                    $var->default(DB::raw('CURRENT_TIMESTAMP'));
                } else if($field->required) {
                    $var->default("1970-01-01 01:01:01");
                } else {
                    $var->default($field->defaultvalue);
                }
                break;
            case 'Date_picker':
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->date($field->name)->change();
                    } else {
                        $var = $table->date($field->name)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->date($field->name);
                    } else {
                        $var = $table->date($field->name)->nullable();
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
            case 'Datetime_picker':
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->dateTime($field->name)->change();
                    } else {
                        $var = $table->dateTime($field->name)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->dateTime($field->name);
                    } else {
                        $var = $table->dateTime($field->name)->nullable();
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
            case 'Date_range':
                if($update) {
                    // Timestamp Edit Not working - http://stackoverflow.com/questions/34774628/how-do-i-make-doctrine-support-timestamp-columns
                    // Error Unknown column type "timestamp" requested. Any Doctrine type that you use has to be registered with \Doctrine\DBAL\Types\Type::addType()
                    // $var = $table->timestamp($field->name)->change();
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->string($field->name);
                    } else {
                        $var = $table->string($field->name)->nullable();
                    }
                }
                // $table->timestamp('created_at')->useCurrent();
                if($field->defaultvalue == NULL || $field->defaultvalue == "" || $field->defaultvalue == "NULL") {
                    $var->default(NULL);
                } else if($field->defaultvalue == "now()") {
                    $var->default(DB::raw('CURRENT_TIMESTAMP'));
                } else if($field->required) {
                    $var->default("1970-01-01 01:01:01");
                } else {
                    $var->default($field->defaultvalue);
                }
                break;
            case 'Decimal':
                $var = null;
                if($update) {
                    $var = $table->decimal($field->name, 15, 3)->change();
                } else {
                    $var = $table->decimal($field->name, 15, 3);
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("0.0");
                }
                break;
            case 'Select2':
                if($field->json_values == "") {
                    if(is_int($field->defaultvalue)) {
                        if($update) {
                            $var = $table->integer($field->name)->unsigned()->nullable()->change();
                        } else {
                            $var = $table->integer($field->name)->unsigned()->nullable();
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
                    }
                }
                $json_values = json_decode($field->json_values, true);
                if(\Str::startsWith($field->json_values, "@")) {
                    $foreign_table_name = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', str_replace("@", "", $field->json_values))), '_'));
                    if($update) {
                        $var = $table->integer($field->name)->nullable()->unsigned()->change();
                        if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                            $var->default(NULL);
                        } else {
                            $var->default($field->defaultvalue);
                        }
                        $table->dropForeign($field->module_obj->table . "_" . $field->name . "_foreign");
                        $table->foreign($field->name)->references('id')->on($foreign_table_name)->onUpdate('cascade')->onDelete('cascade');
                    } else {
                        $var = $table->integer($field->name)->nullable()->unsigned();
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
                        } else {

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
                } else if(is_object($json_values)) {
                    // ############### Remaining
                    if($update) {
                        $var = $table->integer($field->name)->nullable()->unsigned()->change();
                    } else {
                        $var = $table->integer($field->name)->nullable()->unsigned();
                    }
                    // if(is_int($field->defaultvalue)) {
                    //     $var->default($field->defaultvalue);
                    //     break;
                    // }
                }
                break;
            case 'Select2_from_array':
                $json_values = json_decode($field->json_values, true);
                if(is_array($json_values)) {
                    if(isset($field->input_type) && ($field->input_type == "enum" || $field->input_type == "ENUM")) {
                        if($update) {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name)->change();
                            } else {
                                $var = $table->string($field->name)->nullable()->change();
                            }
                        } else {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name)->change();
                            } else {
                                $var = $table->string($field->name)->nullable()->change();
                            }
                        }
                        if($field->defaultvalue != "" && in_array($field->defaultvalue, $json_values)) {
                            $var->default($field->defaultvalue);
                        } else if($field->required) {
                            $var->default(NULL);
                        }
                    } else {
                        if($update) {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name)->change();
                            } else {
                                $var = $table->string($field->name)->nullable()->change();
                            }
                        } else {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name);
                            } else {
                                $var = $table->string($field->name)->nullable();
                            }
                        }
                        if($field->defaultvalue != "") {
                            $var->default($field->defaultvalue);
                        } else if($field->required) {
                            $var->default("");
                        }
                    }
                } else if(is_object($json_values)) {
                    // ############### Remaining
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->integer($field->name)->unsigned()->change();
                        } else {
                            $var = $table->integer($field->name)->nullable()->unsigned()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->integer($field->name)->unsigned();
                        } else {
                            $var = $table->integer($field->name)->nullable()->unsigned();
                        }
                    }
                    // if(is_int($field->defaultvalue)) {
                    //     $var->default($field->defaultvalue);
                    //     break;
                    // }
                }
                break;
            case 'Select2_from_ajax':
                if($field->json_values == "") {
                    if(is_int($field->defaultvalue)) {
                        if($update) {
                            $var = $table->integer($field->name)->unsigned()->nullable()->change();
                        } else {
                            $var = $table->integer($field->name)->unsigned()->nullable();
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
                    }
                }
                $json_values = json_decode($field->json_values, true);
                if(\Str::startsWith($field->json_values, "@")) {
                    $foreign_table_name = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', str_replace("@", "", $field->json_values))), '_'));
                    if($update) {
                        $var = $table->integer($field->name)->nullable()->unsigned()->change();
                        if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                            $var->default(NULL);
                        } else {
                            $var->default($field->defaultvalue);
                        }
                        $table->dropForeign($field->module_obj->table . "_" . $field->name . "_foreign");
                        $table->foreign($field->name)->references('id')->on($foreign_table_name)->onUpdate('cascade')->onDelete('cascade');
                    } else {
                        $var = $table->integer($field->name)->nullable()->unsigned();
                        if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                            $var->default(NULL);
                        } else {
                            $var->default($field->defaultvalue);
                        }
                        $table->foreign($field->name)->references('id')->on($foreign_table_name)->onUpdate('cascade')->onDelete('cascade');
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
                        } else {

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
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->text($field->name)->change();
                        } else {
                            $var = $table->text($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->text($field->name);
                        } else {
                            $var = $table->text($field->name)->nullable();
                        }
                    }
                } else {
                    if($update) {
                        $var = $table->string($field->name, $field->maxlength)->nullable()->change();
                    } else {
                        $var = $table->string($field->name, $field->maxlength)->nullable();
                    }
                    if($field->defaultvalue != "") {
                        $var->default($field->defaultvalue);
                    } else if($field->required) {
                        $var->default("");
                    }
                }
                break;
            case 'Select':
                if($field->json_values == "") {
                    if(is_int($field->defaultvalue)) {
                        if($update) {
                            $var = $table->integer($field->name)->unsigned()->nullable()->change();
                        } else {
                            $var = $table->integer($field->name)->unsigned()->nullable();
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
                    }
                }
                $json_values = json_decode($field->json_values, true);
                if(\Str::startsWith($field->json_values, "@")) {
                    $foreign_table_name = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', str_replace("@", "", $field->json_values))), '_'));
                    if($update) {
                        $var = $table->integer($field->name)->nullable()->unsigned()->change();
                        if($field->defaultvalue == "" || $field->defaultvalue == "0") {
                            $var->default(NULL);
                        } else {
                            $var->default($field->defaultvalue);
                        }
                        $table->dropForeign($field->module_obj->table . "_" . $field->name . "_foreign");
                        $table->foreign($field->name)->references('id')->on($foreign_table_name)->onUpdate('cascade')->onDelete('cascade');
                    } else {
                        $var = $table->integer($field->name)->nullable()->unsigned();
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
                        } else {

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
                } else if(is_object($json_values)) {
                    // ############### Remaining
                    if($update) {
                        $var = $table->integer($field->name)->nullable()->unsigned()->change();
                    } else {
                        $var = $table->integer($field->name)->nullable()->unsigned();
                    }
                    // if(is_int($field->defaultvalue)) {
                    //     $var->default($field->defaultvalue);
                    //     break;
                    // }
                }
                break;
            case 'Select_from_array':
                $json_values = json_decode($field->json_values, true);
                if(is_array($json_values)) {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name)->change();
                        } else {
                            $var = $table->string($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name);
                        } else {
                            $var = $table->string($field->name)->nullable();
                        }
                    }
                    if($field->defaultvalue != "") {
                        $var->default($field->defaultvalue);
                    } else if($field->required) {
                        $var->default("");
                    }
                } else if(is_object($json_values)) {
                    // ############### Remaining
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->integer($field->name)->unsigned()->change();
                        } else {
                            $var = $table->integer($field->name)->nullable()->unsigned()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->integer($field->name)->unsigned();
                        } else {
                            $var = $table->integer($field->name)->nullable()->unsigned();
                        }
                    }
                    // if(is_int($field->defaultvalue)) {
                    //     $var->default($field->defaultvalue);
                    //     break;
                    // }
                }
                break;
            case 'Table': 
                if(isset($json_values) && is_array($json_values)) {
                    if($update) {
                        if(isset($field->json_values) && is_array(json_decode($field->json_values))) {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name)->change();
                            } else {
                                $var = $table->string($field->name)->nullable()->change();
                            }
                        } else {

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
            case 'Email':
                $var = null;
                if($field->maxlength == 0) {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, 100)->change();
                        } else {
                            $var = $table->string($field->name, 100)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, 100);
                        } else {
                            $var = $table->string($field->name, 100)->nullable();
                        }
                    }
                } else {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength)->change();
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength);
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable();
                        }
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'File':
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->integer($field->name)->change();
                    } else {
                        $var = $table->integer($field->name)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->integer($field->name);
                    } else {
                        $var = $table->integer($field->name)->nullable();
                    }
                }
                if($field->defaultvalue != "" && is_numeric($field->defaultvalue)) {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default(NULL);
                }
                break;
            case 'Files':
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->string($field->name, 256)->change();
                    } else {
                        $var = $table->string($field->name, 256)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->string($field->name, 256);
                    } else {
                        $var = $table->string($field->name, 256)->nullable();
                    }
                }
                if(is_string($field->defaultvalue) && \Str::startsWith($field->defaultvalue, "[")) {
                    $var->default($field->defaultvalue);
                } else if(is_array($field->defaultvalue)) {
                    $var->default(json_encode($field->defaultvalue));
                } else {
                    $var->default("[]");
                }
                break;
            case 'Float':
                if($update) {
                    $var = $table->float($field->name)->change();
                } else {
                    $var = $table->float($field->name);
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("0.0");
                }
                break;
            case 'HTML':
                if($update) {
                    $var = $table->string($field->name, 10000)->nullable()->change();
                } else {
                    $var = $table->string($field->name, 10000)->nullable();
                }
                if($field->defaultvalue != null) {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Image':
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->integer($field->name)->change();
                    } else {
                        $var = $table->integer($field->name)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->integer($field->name);
                    } else {
                        $var = $table->integer($field->name)->nullable();
                    }
                }
                if($field->defaultvalue != "" && is_numeric($field->defaultvalue)) {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default(NULL);
                } else {
                    $var->default(NULL);
                }
                break;
            case 'Json':
                if($update) {
                    $var = $table->string($field->name, 256)->change();
                } else {
                    $var = $table->string($field->name, 256);
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
                $var = null;
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->integer($field->name, false)->change();
                    } else {
                        $var = $table->integer($field->name, false)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->integer($field->name, false);
                    } else {
                        $var = $table->integer($field->name, false)->nullable();
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else {
                    $var->default(Null);
                }
                break;
            case 'Phone':
                $var = null;
                if($field->maxlength == 0) {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name)->change();
                        } else {
                            $var = $table->string($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name);
                        } else {
                            $var = $table->string($field->name)->nullable();
                        }
                    }
                } else {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength)->change();
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength);
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable();
                        }
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default(NULL);
                }
                break;
            case 'Multiselect':
                if($update) {
                    $var = $table->string($field->name, 256)->change();
                } else {
                    $var = $table->string($field->name, 256);
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
            case 'Select2_multiple':
                if($update) {
                    $var = $table->string($field->name, 256)->change();
                } else {
                    $var = $table->string($field->name, 256);
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
            case 'Name':
                $var = null;
                if($field->maxlength == 0) {
                    if($update) {
                        $var = $table->string($field->name)->change();
                    } else {
                        $var = $table->string($field->name);
                    }
                } else {
                    if($update) {
                        $var = $table->string($field->name, $field->maxlength)->change();
                    } else {
                        $var = $table->string($field->name, $field->maxlength);
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Password':
                $var = null;
                if($field->maxlength == 0) {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name)->change();
                        } else {
                            $var = $table->string($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name)->nullable();
                        } else {
                            $var = $table->string($field->name);
                        }
                    }
                } else {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength)->change();
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength);
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable();
                        }
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Radio':
                $var = null;
                if($field->json_values == "") {
                    if(is_int($field->defaultvalue)) {
                        if($update) {
                            $var = $table->integer($field->name)->unsigned()->change();
                        } else {
                            $var = $table->integer($field->name)->unsigned();
                        }
                        $var->default($field->defaultvalue);
                        break;
                    } else if(is_string($field->defaultvalue)) {
                        if($update) {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name)->change();
                            } else {
                                $var = $table->string($field->name)->nullable()->change();
                            }
                        } else {
                            if($field->required && $nullable_required) {
                                $var = $table->string($field->name);
                            } else {
                                $var = $table->string($field->name)->nullable();
                            }
                        }
                        $var->default($field->defaultvalue);
                        break;
                    }
                } else if(is_string($field->json_values) && \Str::startsWith($field->json_values, "@")) {
                    if($update) {
                        $var = $table->integer($field->name)->unsigned()->change();
                    } else {
                        $var = $table->integer($field->name)->unsigned();
                    }
                    break;
                }
                $json_values = json_decode($field->json_values, true);
                if(is_array($json_values)) {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name)->change();
                        } else {
                            $var = $table->string($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name);
                        } else {
                            $var = $table->string($field->name)->nullable();
                        }
                    }
                    if($field->defaultvalue != "") {
                        $var->default($field->defaultvalue);
                    } else if($field->required) {
                        $var->default("");
                    }
                } else if(is_object($json_values)) {
                    // ############### Remaining
                    if($update) {
                        $var = $table->integer($field->name)->unsigned()->change();
                    } else {
                        $var = $table->integer($field->name)->unsigned();
                    }
                    // if(is_int($field->defaultvalue)) {
                    //     $var->default($field->defaultvalue);
                    //     break;
                    // }
                }
                break;
            case 'Text':
                $var = null;
                if($field->maxlength == 0) {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name)->change();
                        } else {
                            $var = $table->string($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name);
                        } else {
                            $var = $table->string($field->name)->nullable();
                        }
                    }
                } else {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength)->change();
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength);
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable();
                        }
                    }
                }
                if($field->defaultvalue != null) {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Taginput':
                $var = null;
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->string($field->name, 1000)->change();
                    } else {
                        $var = $table->string($field->name, 1000)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->string($field->name, 1000);
                    } else {
                        $var = $table->string($field->name, 1000)->nullable();
                    }
                }
                if(is_string($field->defaultvalue) && \Str::startsWith($field->defaultvalue, "[")) {
                    $field->defaultvalue = json_decode($field->defaultvalue, true);
                }
                
                if(is_string($field->defaultvalue)) {
                    $field->defaultvalue = json_encode([$field->defaultvalue]);
                    //echo "string: ".$field->defaultvalue;
                    $var->default($field->defaultvalue);
                } else if(is_array($field->defaultvalue)) {
                    $field->defaultvalue = json_encode($field->defaultvalue);
                    //echo "array: ".$field->defaultvalue;
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Textarea':
                $var = null;
                if($field->maxlength == 0) {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->text($field->name)->change();
                        } else {
                            $var = $table->text($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->text($field->name);
                        } else {
                            $var = $table->text($field->name)->nullable();
                        }
                    }
                } else {
                    if($update) {
                        $var = $table->string($field->name, $field->maxlength)->nullable()->change();
                    } else {
                        $var = $table->string($field->name, $field->maxlength)->nullable();
                    }
                    if($field->defaultvalue != "") {
                        $var->default($field->defaultvalue);
                    } else if($field->required) {
                        $var->default("");
                    }
                }
                break;
            case 'TextField':
                $var = null;
                if($field->maxlength == 0) {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name)->change();
                        } else {
                            $var = $table->string($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name);
                        } else {
                            $var = $table->string($field->name)->nullable();
                        }
                    }
                } else {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength)->change();
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength);
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable();
                        }
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
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name)->change();
                        } else {
                            $var = $table->string($field->name)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name);
                        } else {
                            $var = $table->string($field->name)->nullable();
                        }
                    }
                } else {
                    if($update) {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength)->change();
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable()->change();
                        }
                    } else {
                        if($field->required && $nullable_required) {
                            $var = $table->string($field->name, $field->maxlength);
                        } else {
                            $var = $table->string($field->name, $field->maxlength)->nullable();
                        }
                    }
                }
                if($field->defaultvalue != "") {
                    $var->default($field->defaultvalue);
                } else if($field->required) {
                    $var->default("");
                }
                break;
            case 'Week':
                break;
            case 'Month':
                if($update) {
                    if($field->required && $nullable_required) {
                        $var = $table->date($field->name)->change();
                    } else {
                        $var = $table->date($field->name)->nullable()->change();
                    }
                } else {
                    if($field->required && $nullable_required) {
                        $var = $table->date($field->name);
                    } else {
                        $var = $table->date($field->name)->nullable();
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
            case 'Browse':
                $var = $table->string($field->name, $field->maxlength)->nullable();
                $var->default(NULL);
                
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
        $out = array();
        foreach($fields as $field) {
            // Check if field format is New
            if(CustomHelper::is_assoc_array($field)) {
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
                $obj = (Object)array();
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
    public static function validateRules($module_name, $request, $isEdit = false)
    {
        $module = self::where('name',$module_name)->first();
        
        $rules = [];
        if(isset($module->id)) {
            $ftypes = FieldType::getFTypes2();
            $add_from = true;
            foreach($module->fields as $field) {
                if($isEdit && !isset($request->{$field['name']})) {
                    $add_from = false;
                } else {
                    $add_from = true;
                }
                if($add_from) {
                    $col = "";
                    if($field['required']) {
                        $col .= "required|";
                    }
                    if(in_array($ftypes[$field['field_type']["id"]], array("Currency", "Decimal"))) {
                    
                        // No min + max length
                    } else {
                        if($field['minlength'] != 0) {
                            $col .= "min:" . $field['minlength'] . "|";
                        }
                        if($field['maxlength'] != 0) {
                            $col .= "max:" . $field['maxlength'] . "|";
                        }
                    }
                    if(in_array($ftypes[$field['field_type']["id"]], ["Datetime_picker","Datetime"])) {
                        $col .= "date_format:Y-m-d H:i:s";
                    }
                    if(in_array($ftypes[$field['field_type']["id"]], ["Date","Date_picker"])) {
                        $col .= "date_format:Y-m-d";
                    }

                    if(in_array($ftypes[$field['field_type']["id"]], array("Email"))) {
                        $col .= "regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/|";
                    }
                    if($field['unique'] && !$isEdit) {
                        $filed_unique_value = isset($request->{$field['name']})? ','.$request->{$field['name']}:"";
                        $col .= "unique:" . $module->table_name.','.$field['name'].$filed_unique_value;
                    } else if($isEdit && $field['unique']) {
                        $col .= "unique:" . $module->table_name.','.$field['name'].','.$request->segment(3);
                    }
                    if(\Str::startsWith($field->json_values, "@") && isset($request->{$field['name']})) {
                        $foreign_table_name = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', str_replace("@", "", $field->json_values))), '_'));
                        $col .= "exists:" . $foreign_table_name .',id';
                    }

                    // 'name' => 'required|unique|min:5|max:256',
                    // 'author' => 'required|max:50',
                    // 'price' => 'decimal',
                    // 'pages' => 'integer|max:5',
                    // 'genre' => 'max:500',
                    // 'description' => 'max:1000'
                    if($col != "") {
                        $rules[$field['name']] = trim($col, "|");
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
        if(\Auth::user()->isSuperAdmin()) {
            return true;
        }

        $roles = array();
        
        if($crud instanceof Page) {
            $module = $crud;
        } else if(is_string($crud) && !isset($crud->module->id)) {
            $crud = self::make($crud);
            $module = $crud->module;
        } else if(isset($crud->module->id)) {
            $module = $crud->module;
        }
        
        if($role_id) {
            $role = \Sagartakle\Laracrud\Models\Role::find($role_id);
            if(isset($role->id)) {
                $roles = $role->roles();
            }
        } else {
            $roles = \Auth::user()->roles();
        }
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
            $roles_arr = Role::where('id', $specific_role)->get();
        } else {
            $roles_arr = Role::all();
        }
        $roles = array();
        
        $arr_field_access = array(
            'invisible' => 0,
            'readonly' => 1,
            'write' => 2
        );
        
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
            
            $role->fields = array();
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
        if(\Auth::user()->isSuperAdmin() || self::hasRoleAccess($crud, $permission)) {
            return true;
        }

        $users = array();

        if($crud instanceof Page) {
            $module = $crud;
        } else if(is_string($crud) && !isset($crud->module->id)) {
            $crud = self::make($crud);
            $module = $crud->module;
        } else if(isset($crud->module->id)) {
            $module = $crud->module;
        }
        
        if(\Auth::user()->isAdmin() && !(isset($crud->name) && in_array($crud->name, ['Employees']))) {
            return true;
        } else if((isset($crud->name) && in_array($crud->name, ['Employees']))) {
            return false;
        }

        if($user_id) {
            $user = \App\User::find($user_id);
            if(isset($user->id)) {
                $user = $user;
            } else {
                $user = [];
            }
        } else {
            $user = \Auth::user();
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
            $users_arr = User::where('id', $specific_user)->get();
        } else {
            $users_arr = User::all();
        }
        $users = array();
        
        $arr_field_access = array(
            'invisible' => 0,
            'readonly' => 1,
            'write' => 2
        );
        
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
            $show_indexs = Field::where('module_id', $module->id)->where('show_index', 1)->get()->toArray();
        } else if(is_string($module_id_name)) {
            $module = self::where('name', $module_id_name)->first();
            $show_indexs = Field::where('module_id', $module->id)->where('show_index', 1)->get()->toArray();
        } else if(isset($module_id_name->module)){
            $show_indexs = collect($module_id_name->fields)->where('show_index', 1)->toArray();
        }
        
        if($isObjects) {
            $id_col = array('label' => 'id', 'name' => 'id');
        } else {
            $id_col = 'id';
        }
        $show_indexs_temp = array($id_col);
        foreach($show_indexs as $col) {
            // if(self::hasFieldAccess($module->id, $col['id'])) {
                if($isObjects) {
                    $show_indexs_temp[] = $col;
                } else {
                    $show_indexs_temp[] = $col['name'];
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
            AccessModule::withTrashed()->updateOrCreate([
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
        return $this->hasMany('Sagartakle\Laracrud\Models\Field', 'module_id', 'id');
    }

    /**
     * Get the fields of this module.
     * Module::field_names_array($name)
     */
    public static function field_names_array($name)
    {
        return self::select('fields.name as name')->where('modules.name',$name)
                ->join('fields','fields.module_id','=','modules.id')->pluck('name');
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
