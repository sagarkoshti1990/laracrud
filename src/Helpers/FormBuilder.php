<?php

namespace Sagartakle\Laracrud\Helpers;

use Schema;
use Illuminate\Support\Str;
use Collective\Html\FormFacade as Form;
use DB;
use Sagartakle\Laracrud\Models\Upload;
use Sagartakle\Laracrud\Models\Page;

/**
 * Class FormBuilder
 *
 */
class FormBuilder
{
    public static $count = [];
    /**
     * view input field enclosed within form.
     *
     * Uses blade syntax @input('name')
     *
     * @param $field_name Field Name for which input has be created
     */
    public static function input($crud, $field_name, $params = [], $default_val = null, $required2 = null)
    {
        $row = null;
        $field = [];
        $fields = $crud->fields;
        // echo "<pre>".json_encode($fields,JSON_PRETTY_PRINT)."</pre>";
        if(isset($fields[$field_name]) && is_array($fields[$field_name])) {
            $fields[$field_name] = (object)($fields[$field_name]);
        }
        
        if(!isset($fields[$field_name]->name)) {
            return;
        }
        $field = collect($fields[$field_name])->toArray();

        if(isset($crud->row) && !in_array($field_name,$crud->model->gethidden()) && !in_array($field_name,['password'])) {
            $row = $crud->row;
            if(isset($crud->row) && is_array($crud->row)) {
                $field['value'] = $fields[$field_name]->value = isset($crud->row[$field_name]) ? $crud->row[$field_name] : null;
            } else {
                $field['value'] = $fields[$field_name]->value = isset($crud->row->$field_name) ? $crud->row->$field_name : null;
            }
        } else {
            if(isset($default_val)) {
                $field['value'] = $default_val;
            }
        }
        // \CustomHelper::ajprint($fields);
        
        if(isset($fields[$field_name]->field_type->name)) {
            $field['type'] = $fields[$field_name]->field_type->name;
        } else if(isset($fields[$field_name]->field_type['name'])){
            $field['type'] = $fields[$field_name]->field_type['name'];
        } else {
            $field['type'] = $fields[$field_name]->field_type;
        }
        
        if(isset($params['prefix'])) {
            $field['prefix'] = $params['prefix'];
        }

        if(isset($params['suffix'])) {
            $field['suffix'] = $params['suffix'];
        }

        if(isset($params['wrapperAttributes'])) {
            $field['wrapperAttributes'] = $params['wrapperAttributes'];
            unset($params['wrapperAttributes']);
        } else if(config('stlc.view.attributes.'.$crud->name.'.'.$field_name.'.wrapperAttributes',null) != null) {
            $field['wrapperAttributes'] = config('stlc.view.attributes.'.$crud->name.'.'.$field_name.'.wrapperAttributes');
        } else if(config('stlc.view.attributes.'.$crud->name.'.wrapperAttributes',null) != null) {
            $field['wrapperAttributes'] = config('stlc.view.attributes.'.$crud->name.'.wrapperAttributes');
        } else if(config('stlc.view.attributes.wrapperAttributes',null) != null) {
            $field['wrapperAttributes'] = config('stlc.view.attributes.wrapperAttributes');
        } else if($field['type'] == 'Radio') {
            $field['wrapperAttributes']['radio_inline'] = true;
        }
        
        if(isset($fields[$field_name]->field_type) && $fields[$field_name]->field_type == "") {
            $field_type = "Text";
        } else if(isset($fields[$field_name]->field_type)) {
            if(isset($fields[$field_name]->field_type->name)) {
                $field_type = $fields[$field_name]->field_type->name;
            } else if(isset($fields[$field_name]->field_type['name'])){
                $field_type = $fields[$field_name]->field_type['name'];
            } else {
                $field_type = $fields[$field_name]->field_type;
            }
        }
        $label = $fields[$field_name]->label ?? ucfirst(str_replace('_',' ',$field_name));
        
        if(isset($field['attributes']) && is_array($field['attributes'])) {
            $field['attributes'] = array_replace($field['attributes'],$params);
            if(!isset($field['attributes']['class'])) {
                $field['attributes']['class'] = 'form-control';
            }
            if(!isset($field['attributes']['placeholder'])) {
                $field['attributes']['placeholder'] = $label;
            }
        } else {
            $field['attributes'] = ['placeholder' => $label,'class' => 'form-control'];
            if(config('stlc.view.attributes.'.$crud->name.'.'.$field_name.'.field_attributes',null) != null) {
                $field['attributes'] = array_replace($field['attributes'],config('stlc.view.attributes.'.$crud->name.'.'.$field_name.'.field_attributes'));
            } else if(config('stlc.view.attributes.'.$crud->name.'.field_attributes',null) != null) {
                $field['attributes'] = array_replace($field['attributes'],config('stlc.view.attributes.'.$crud->name.'.field_attributes'));
            } else if(config('stlc.view.attributes.field_attributes',null) != null) {
                $field['attributes'] = array_replace($field['attributes'],config('stlc.view.attributes.field_attributes'));
            }
        }
        
        $unique = $fields[$field_name]->unique ?? false;
        $field['default'] = $fields[$field_name]->defaultvalue ?? Null;
        $minlength = $fields[$field_name]->minlength ?? "0";
        $maxlength = $fields[$field_name]->maxlength ?? "0";

        if(isset($required2)) {
            $required = $required2;
        } else if(isset($params['required']) && ($params['required'] == true || $params['required'] == false)) {
            $required = $params['required'];
        } else {
            $required = $fields[$field_name]->required ?? false;
        }
        
        if($required) {
            $field['label'] = $label." <span style='color:red;'>*</span>";
        } else {
            $field['label'] = $label;
        }

        if(isset($fields[$field_name]->json_values)) {
            if(is_array($fields[$field_name]->json_values)) {
                $json_values = json_encode($fields[$field_name]->json_values);
            } else {
                $json_values = $fields[$field_name]->json_values;
            }
        } else {
            $json_values = null;
        }

        if(isset($json_values) && !empty($json_values) && is_string($json_values) && \Str::startsWith($json_values, "@")) {
            $module = \Module::where('name', str_replace("@", "", $json_values))->first();
            if(!isset($module->model)) {
                $json_values_arr = explode('|',$fields[$field_name]->json_values);
                $module = (object)[];
                $module->model = collect(str_replace("@", "", $json_values_arr))->first();
                if(class_exists($module->model)) {
                    $module->table_name = (new $module->model)->getTable();
                    $module->represent_attr = collect(str_replace("|", "", $json_values_arr))->last();
                } else {
                    $module = null;
                }
            }
        }
        
        $out = '';
        // $field = collect($field)->forget(['id','field_type','name','crud_id','field_type_id']);
        // \CustomHelper::ajprint($field,false);

        switch($field_type) {
            case 'Decimal':
            case 'Float':
            case 'Number':
            case 'Currency':
                if(isset($minlength) && $minlength) {
                    $params['min'] = $minlength;
                }
                if(isset($maxlength) && $maxlength) {
                    $params['max'] = $maxlength;
                }
                if(in_array($field_type,['Float','Decimal','Currency'])) {
                    if($field_type == 'Currency') {
                        $field['prefix'] = '<i class="fa fa-rupee-sign"></i>';
                    } else {
                        $field['prefix'] = ($field_type == 'Decimal') ? '<b>D</b>' : '<b>F</b>';
                    }
                    $field['attributes'] = array_replace($field['attributes'],['step' => 0.01]);
                }
                $field_type = 'Number';
                break;
            case 'Phone':
                if(isset($minlength) && $minlength) {
                    $params['minlength'] = $minlength;
                }
                if(isset($maxlength) && $maxlength) {
                    $params['maxlength'] = $maxlength;
                }
                break;
            case 'Polymorphic_select':
                if(isset($minlength) && $minlength) {
                    $params['minlength'] = $minlength;
                }
                if(isset($maxlength) && $maxlength) {
                    $params['maxlength'] = $maxlength;
                }
                break;
            case 'Polymorphic_multiple':
                $field['attributes']['class'] .= " select2_multiple";
                $field['attributes']['placeholder'] = 'Select ' . $label;
                if(isset($module)) {
                    $ralasion_field = $module->fields->firstWhere('name',($module->represent_attr ?? ""));
                    $polymorphic_module = $ralasion_field->getJsonModule();
                    $polymorphic_field = $module->fields->firstWhere('field_type.name','Polymorphic_select');
                    if(isset($params['query']) || isset($fields[$field_name]->query)) {
                        $field['model'] = $params['query'] ?? $fields[$field_name]->query;
                        unset($params['query']);
                    } else {
                        if(isset($polymorphic_module->id)) {
                            $field['model'] = $polymorphic_module->model ?? null;
                        }
                    }
                    if(isset($polymorphic_module->id) && isset($row) && $row instanceof \Illuminate\Database\Eloquent\Model) {
                        $field['value'] = $row->morphToMany($polymorphic_module->model, $polymorphic_field->name,$module->table_name)->where('attribute',$field_name)->get()->pluck('id')->toJson();
                        // \CustomHelper::ajprint($field['value']);
                    }
                    if(isset($params['attribute']) && is_array($params['attribute'])) {
                        $field['attribute'] = $params['attribute'];
                    } else {
                        $field['attribute'] = $polymorphic_module->represent_attr ?? "";
                    }
                    $field['options'] = $json_values;
                }
                $field_type = 'Select2_multiple';
                break;
            case 'Radio':
                $field['inline'] = 1;
                $field['attributes']['class'] .=" flat-green";
                if(isset($module) && isset($module->model)) {
                    $field['model'] = $module->model ?? null;
                    if(isset($params['attribute']) && is_array($params['attribute'])) {
                        $field['attribute'] = $params['attribute'];
                    } else {
                        $field['attribute'] = $field['attribute'] ?? $module->represent_attr;
                    }
                } else if(is_array(json_decode($json_values))) {
                    $arr = [];
                    $collection = collect(json_decode($json_values));
                    foreach ($collection as $key => $value) {
                        $arr[$value] = $value;
                    }
                    $field['options'] = $arr;
                } else {
                    $field['options'] = ['No', 'Yes'];
                }
                break;
            case 'Checkbox':
                $field['attributes']['class'] .= " form-check-input";
                $field['inline'] = 1;
                if(!\Str::startsWith($json_values, "@") && is_array(json_decode($json_values))) {
                    $arr = [];
                    $collection = collect(json_decode($json_values));
                    foreach ($collection as $key => $value) {
                        $arr[$value] = $value;
                    }
                    $field['options'] = $arr;
                } else {
                    $field['options'] = ['Yes', 'No'];
                }
                break;
            case 'Ckeditor':
                if(isset(self::$count['Ckeditor'])) {
                    self::$count['Ckeditor']++;
                } else {
                    self::$count['Ckeditor'] = 0;
                }
                if(isset($params['only_button']) && is_array($params['only_button'])) {
                    $field['attribute'] = $params['only_button'];
                }
                unset($params['only_button']);
                
                if(!isset($params['id'])) {
                    $params['id'] = 'ckeditor-'.$field['name'].'-'.$crud->name;
                }
                if(isset($required) && $required) {
                    $field['attributes']['class'] .= " ckeditor_required";
                }
                break;
            case 'Month':
                $field['attributes']['class'] .= " month_combodate";
                break;
            case 'Select':
                if(isset($module) && isset($module->model)) {
                    $field['model'] = $module->model ?? null;
                    if(isset($params['attribute']) && is_array($params['attribute'])) {
                        $field['attribute'] = $params['attribute'];
                    } else {
                        $field['attribute'] = $field['attribute'] ?? $module->represent_attr;
                    }
                    $field['options'] = $json_values;
                    if(isset($required) && $required) {
                        $field['allows_null'] = false;
                    } else {
                        $field['allows_null'] = true;
                    }
                } else {
                    $arr = [];
                    $collection = collect(json_decode($json_values));
                    foreach ($collection as $key => $value) {
                        $arr[$value] = $value;
                    }
                    $field['options'] = $arr;
                    if(isset($required) && $required) {
                        $field['allows_null'] = false;
                    } else {
                        $field['allows_null'] = true;
                    }
                }
                break;
            case 'Multiselect':
                if(isset($module)) {
                    $field['model'] = $module->model ?? null;
                    if(isset($params['attribute']) && is_array($params['attribute'])) {
                        $field['attribute'] = $params['attribute'];
                    } else {
                        $field['attribute'] = $field['attribute'] ?? $module->represent_attr;
                    }
                    $field['options'] = $json_values;
                } else {
                    $arr = [];
                    $collection = collect(json_decode($json_values));
                    foreach ($collection as $key => $value) {
                        $arr[$value] = $value;
                    }
                    $field['options'] = $arr;
                }
                
                if(isset($required) && $required) {
                    $field['allows_null'] = false;
                } else {
                    $field['allows_null'] = true;
                }
                break;
            case 'Select_from_array':
                $arr = [];
                $collection = collect(json_decode($json_values));
                foreach ($collection as $key => $value) {
                    $arr[$value] = $value;
                }
                
                $field['options'] = $arr;
                if(isset($required) && $required) {
                    $field['allows_null'] = false;
                } else {
                    $field['allows_null'] = true;
                }
                break;
            case 'Select2':
                if(isset($module) && isset($module->model)) {
                    $field['attributes']['class'] .= " select2_field";
                    if(isset($params['query']) || isset($fields[$field_name]->query)) {
                        $field['model'] = $params['query'] ?? $fields[$field_name]->query;
                        unset($params['query']);
                    } else {
                        $field['model'] = $module->model ?? null;
                    }
                    if(isset($params['attribute']) && is_array($params['attribute'])) {
                        $field['attribute'] = $params['attribute'];
                    } else {
                        $field['attribute'] = $field['attribute'] ?? $module->represent_attr;
                    }
                    
                    $field['options'] = $json_values;
                    if(isset($required) && $required) {
                        $field['allows_null'] = false;
                    } else {
                        $field['allows_null'] = true;
                    }
                } else {
                    $arr = [];
                    $collection = collect(json_decode($json_values));
                    foreach ($collection as $key => $value) {
                        $arr[$value] = $value;
                    }
                    $field['attributes']['class'] .= " select2_field";
                    $field['options'] = $arr;
                    if(isset($required) && $required) {
                        $field['allows_null'] = false;
                    } else {
                        $field['allows_null'] = true;
                    }
                }
                
                break;
            case 'Select2_multiple':
                $field['attributes']['class'] .= " select2_multiple";
                $field['attributes']['placeholder'] = 'Select ' . $label;
                if(isset($module)) {
                    if(isset($params['query']) || isset($fields[$field_name]->query)) {
                        $field['model'] = $params['query'] ?? $fields[$field_name]->query;
                        unset($params['query']);
                    } else {
                        $field['model'] = $module->model ?? null;
                    }
                    if(isset($params['attribute']) && is_array($params['attribute'])) {
                        $field['attribute'] = $params['attribute'];
                    } else {
                        $field['attribute'] = $field['attribute'] ?? $module->represent_attr;
                    }
                    $field['options'] = $json_values;
                } else {
                    $arr = [];
                    $collection = collect(json_decode($json_values));
                    foreach ($collection as $key => $value) {
                        $arr[$value] = $value;
                    }
                    $field['attributes']['class'] .= " select2_multiple";
                    $field['options'] = $arr;
                }
                
                if(isset($required) && $required) {
                    $field['allows_null'] = false;
                } else {
                    $field['allows_null'] = true;
                }
                break;
            case 'Select2_from_array':
                $arr = [];
                $collection = collect(json_decode($json_values));
                foreach ($collection as $key => $value) {
                    $arr[$value] = $value;
                }
                $field['attributes']['class'] .= " select2_from_array";
                $field['options'] = $arr;
                if(isset($required) && $required) {
                    $field['allows_null'] = false;
                } else {
                    $field['allows_null'] = true;
                }
                break;
            case 'Select2_from_array_multiple':
                $arr = [];
                $collection = collect(json_decode($json_values));
                foreach ($collection as $key => $value) {
                    $arr[$value] = $value;
                }
                $field['attributes']['class'] .= " select2_from_array";
                $field['options'] = $arr;
                if(isset($required) && $required) {
                    $field['allows_null'] = false;
                } else {
                    $field['allows_null'] = true;
                }
            break;
            case 'Select2_from_ajax':
                if(isset($module) && isset($module->model)) {
                    $field['model'] = $module->model ?? null;
                    if(isset($params['attribute']) && is_array($params['attribute'])) {
                        $field['attribute'] = $params['attribute'];
                    } else {
                        $field['attribute'] = $field['attribute'] ?? $module->represent_attr;
                    }
                    $field['options'] = $json_values;
                    if(isset($required) && $required) {
                        $field['allows_null'] = false;
                    } else {
                        $field['allows_null'] = true;
                    }
                    $field['data_source'] = url(config('stlc.route_prefix', 'admin').'/'.$module->table_name);
                } else {
                    $arr = [];
                    $collection = collect(json_decode($json_values));
                    foreach ($collection as $key => $value) {
                        $arr[$value] = $value;
                    }
                    $field['attributes']['class'] .= " select2_field";
                    $field['options'] = $arr;
                    if(isset($required) && $required) {
                        $field['allows_null'] = false;
                    } else {
                        $field['allows_null'] = true;
                    }
                }
                break;
            case 'Select2_tags':
                $arr = [];
                $collection = collect(json_decode($json_values));
                foreach ($collection as $key => $value) {
                    $arr[$value] = $value;
                }
                $field['attributes']['class'] .= " select2_field_tag";
                $field['options'] = $arr;
                if(isset($required) && $required) {
                    $field['allows_null'] = false;
                } else {
                    $field['allows_null'] = true;
                }
                break;
            case 'Select2_multiple_tags':
                $arr = [];
                $collection = collect(json_decode($json_values));
                foreach ($collection as $key => $value) {
                    $arr[$value] = $value;
                }
                if(isset($field['value']) && is_array(json_decode($field['value']))) {
                    $collection = collect(json_decode($field['value']));
                    foreach ($collection as $key => $value) {
                        $arr[$value] = $value;
                    }
                }
                $field['attributes']['class'] .= " select2_field_tag";
                $field['options'] = $arr;
                if(isset($required) && $required) {
                    $field['allows_null'] = false;
                } else {
                    $field['allows_null'] = true;
                }
                $field['multiple'] = true;
                break;
            case 'Table':
                $field['columns'] = collect(json_decode($json_values));
                break;
            case 'File':
                if(isset($params['file_type'])) {
                    $field['file_type'] = $params['file_type']; 
                    unset($params['file_type']);
                }
                break;
            case 'Files':
                if(isset($params['file_type'])) {
                    $field['file_type'] = $params['file_type']; 
                    unset($params['file_type']);
                }
                $module = $fields[$field_name]->getJsonModule();
                if(isset($module)) {
                    $ralasion_field = $module->fields->firstWhere('name',($module->represent_attr ?? ""));
                    $polymorphic_module = $ralasion_field->getJsonModule();
                    $polymorphic_field = $module->fields->firstWhere('field_type.name','Polymorphic_select');
                    
                    if(isset($polymorphic_module->id) && isset($row) && $row instanceof \Illuminate\Database\Eloquent\Model) {
                        $field['value'] = $row->morphToMany($polymorphic_module->model, $polymorphic_field->name,$module->table_name)->where('attribute',$field_name)->get()->pluck('id')->toArray();
                        // \CustomHelper::ajprint($field['value']);
                    }
                }
                break;
            case 'Json':
                $arr = [];
                $collection = collect(json_decode($json_values));
                foreach ($collection as $key => $value) {
                    $arr[$value] = $value;
                }
                
                $field['options'] = $arr;
                $params['input_type'] = 'text';
                break;
            case 'Password':
                
            break;
        }
        
        if(isset($params) && count($params)) {
            $params = collect($params)->except(['attribute'])->all();
        }
        
        if(!isset($params['placeholder'])) {
            if(isset($field['attributes']['placeholder'])) {
                $params['placeholder'] = $field['attributes']['placeholder'];
            }
        }

        if($unique && !isset($params['unique'])) {
            $params['data-rule-unique'] = "true";
            $params['field_id'] = $fields[$field_name]['id'];
            $params['prefixRoute'] = config('stlc.route_prefix');
            if(!isset($params['isEdit']) && !isset($params['row_id'])) {
                if(isset($row)) {
                    $params['isEdit'] = true;
                    $params['row_id'] = isset($row->id) ? $row->id : $row['id'];
                } else {
                    $params['isEdit'] = false;
                    $params['row_id'] = 0;
                }
            }
            // $out .= '<input type="hidden" name="_token_' . $module->fields[$field_name]['id'] . '" value="' . csrf_token() . '">';
        }
        
        if($required && !isset($params['required'])) {
            $params['required'] = $required;
        } else if(isset($params['required']) && $params['required'] == false){
            unset($params['required']);
        }
        
        if(isset($params['name']) && is_string($params['name'])) {
            $field['name'] = $params['name'];
        }
        
        $field['attributes'] = array_replace($field['attributes'],$params);

        switch($field_type) {
            case 'Currency':
                $field_type = 'Number';
            break;
            case 'Select2_multiple_tags':
                $field_type = 'Select2_tags';
            break;
        }
        
        if(isset($field_type) && $field_type == "") {
            $out .= view(config('stlc.view_path.fields.Hidden','stlc::fields.Hidden'), array("field" => $field, "fields" => $fields, "crud" => $crud))->render();
        } else if(isset($field_type) && $field_type != "") {
            $out .= view(config('stlc.view_path.fields.'.$field_type,'stlc::fields.'.$field_type), array("field" => $field, "fields" => $fields, "crud" => $crud))->render();
        }
        
        return $out;
    }
    
    /**
     * Display field is CRUDs View show.blade.php with Label
     *
     * Uses blade syntax @display('name')
     *
     * @param $crud Module Object
     * @param $field_name Field Name for which display has be created
     * @return string This return html string with field display with Label
     */
    public static function display($crud, $field_name, $arr = ['class' => 'row'], $labaleclass = "col-md-4 col-sm-6 col-xs-6", $valueclass = 'col-md-8 col-sm-6 col-xs-6')
    {
        // Check Field View Access
        // if(\Module::hasFieldAccess($crud->id, $crud->fields[$field_name]['id'], $access_type = "view")) {
            $fields = $crud->fields;
            if(!isset($fields[$field_name]->name)) {
                return;
            }
            if(isset($arr['label'])) {
                $label = $arr['label'];
            } else {
                $label = $fields[$field_name]->label;
            }

            $row = null;
            $out = "";
            if(isset($crud->row)) {
                $row = $crud->row;
            }
            
            $out .= '<div class="'.(isset($arr['class'])?$arr['class']:'row').'">';
            $out .= '<label for="' . $field_name . '" class="'.$labaleclass.' font-weight-bold">' . $label . ' </label>';
            
            $value = self::get_field_value($crud, $field_name);
            $out .= '<div class="'.$valueclass.' fvalue">' . $value . '</div>';

            $out .= '</div>';
            return $out;
        // } else {
        //     return "";
        // }
    }
    
    public static function get_field_value($crud, $field_name,$field_type = Null,$html = Null)
    {
        $fields = $crud->fields;
        $item = [];
        $value = '';
        if(isset($crud) && is_array($crud) && isset($crud['row'])) {
            $item = $crud['row'];
            if(gettype($item) == "object" && isset($item->{$field_name})) {
                $value = $item->{$field_name};
            } else if(gettype($item) == "array" && isset($item[$field_name])) {
                $value = $item[$field_name];
            }
        } else if(isset($crud) && is_object($crud) && isset($crud->row)) {
            $item = $crud->row;
            if(gettype($item) == "object" && isset($item->{$field_name})) {
                $value = $item->{$field_name};
            } else if(gettype($item) == "array" && isset($item[$field_name])) {
                $value = $item[$field_name];
            }
        }
        
        if(isset($field_type)) {
            $field_type = $field_type;
        } else if(isset($fields[$field_name]->field_type->name)) {
            $field_type = $fields[$field_name]->field_type->name;
        } else if(isset($fields[$field_name]->field_type)){
            $field_type = $fields[$field_name]->field_type;
        } else if(substr($field_name,-3) == '_id' && ( isset($item[substr($field_name,0,-3).'_type']) || isset($item->{substr($field_name,0,-3).'_type'}) ) ){
            $field_name = substr($field_name,0,-3);
            $field_type = 'Polymorphic_select';
        } else {
            $field_type = '';
        }
        // echo json_encode($field_type);
        
        switch($field_type) {
            case 'Checkbox':
                $data = "";
                if(isset($value) && is_array(json_decode($value))) {
                    foreach(json_decode($value) as $val) {
                        $data .= '<span class="badge large bg-purple mr-1">'.$val.'</span>';
                    }
                }
                $value = $data;

                break;
            case 'Ckeditor':
                $value = html_entity_decode($value);
                
                break;
            case 'Date':
                $value = \CustomHelper::date_format($value);

                break;
            case 'Date_picker':
                $value = \CustomHelper::date_format($value);

                break;
            case 'Datetime':
                $value = \CustomHelper::date_format($value, 'field_show_with_time');

                break;
            case 'Datetime_picker':
                $value = \CustomHelper::date_format($value, 'field_show_with_time');

                break;
            case 'Email':
                $value = '<a href="mailto:'.$value.'">'.$value.'</a>';

                break;
            case 'File':
                $img = "";
                if((isset($value) && $value)) {
                    $upload = config('stlc.upload_model')::find($value);
                    if(isset($upload->id)) {
                        $url_file = url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name);
                        $img = \CustomHelper::showHtml($value,'uploaded_file text-wrap my-1 mr-2 align-top',false);
                    }
                }
                if(isset($html) && $html == true) {
                    $value = $url_file;
                } else {
                    $value = $img;
                }

                break;
            case 'Files':
                $img = "";
                $module = $fields[$field_name]->getJsonModule();
                if(isset($module->id)) {
                    $ralasion_field = $module->fields->firstWhere('name',($module->represent_attr ?? ""));
                    $polymorphic_module = $ralasion_field->getJsonModule();
                    $polymorphic_field = $module->fields->firstWhere('field_type.name','Polymorphic_select');
                    if(isset($polymorphic_module->id)) {
                        if(isset($item) && $item instanceof \Illuminate\Database\Eloquent\Model) {
                            $uploads = $item->morphToMany($polymorphic_module->model, $polymorphic_field->name,$module->table_name)->where('attribute',$field_name)->get();
                            $img = "<div class='uploaded_files'>";
                            $url_file = [];
                            foreach ($uploads as $key => $upload) {
                                if(isset($upload->id)) {
                                    if(isset($html) && $html == "value") {
                                        $url_file[] = url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name);
                                    } else {
                                        $img .= \CustomHelper::showHtml($upload->id,'uploaded_file2 d-inline-block position-relative my-1 mr-2 align-top text-wrap',false);
                                    }
                                }
                            }
                            $img .= "</div>";
                        }
                    }
                }
                if(isset($html) && $html == "value") {
                    $value = $url_file;
                } else {
                    $value = $img;
                }

                break;
            case 'Hidden':
                if(\Str::startsWith($fields[$field_name]->json_values, "@")) {
                    $module = \Module::where('name',substr($fields[$field_name]->json_values, 1))->first();
                    if(!isset($module->model)) {
                        $json_values_arr = explode('|',$fields[$field_name]->json_values);
                        $module = (object)[];
                        $module->model = collect(str_replace("@", "", $json_values_arr))->first();
                        $module->table_name = (new $module->model)->getTable();
                        $module->represent_attr = collect(str_replace("|", "", $json_values_arr))->last();
                    }
                    $represent_attr = $module->represent_attr;
                    if(isset($value) && !empty($value)) {
                        //$value = DB::table($module->table_name)->where('id', $value)->first()->$represent_attr;
                        $test_val = DB::table($module->table_name)->where('id', $value)->first();
                        if(is_object($test_val)) {
                            if($module->name == 'Users') {
                                $value = $test_val->{'title'}." ".$test_val->{'first_name'}." ".$test_val->{'last_name'};
                            } else {
                                $value = $test_val->{$represent_attr};
                            }
                        } elseif(is_array($test_val)) {
                            if($module->name == 'Users') {
                                $value = $test_val->{'title'}." ".$test_val->{'first_name'}." ".$test_val->{'last_name'};
                            } else {
                                $value = $test_val[$represent_attr];
                            }
                        }
                    }
                }

                break;
            case 'Image':
                if($value != 0 && $value != "0") {
                    $upload = config('stlc.upload_model')::find($value);
                    if(isset($upload->id)) {
                        if(isset($html) && $html == "value") {
                            $value = url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name);
                        } else {
                            $value = \CustomHelper::showHtml($value,'uploaded_file text-wrap my-1 mr-2 align-top',false);
                        }
                    } else {
                        $value = 'Uploaded image not found.';
                    }
                } else {
                    $value = 'No Image';
                }

                break;
            case 'Json':
                $data = "";
                if(isset($value) && is_object(json_decode($value))) {
                    foreach(json_decode($value) as $key => $val) {
                        $data .= '<div class="row">';
                        $data .= '<label for="'.$key.'" class="col-md-4 col-sm-6 col-xs-6 font-weight-bold">'.$key.'</label>';
                        $data .= '<div class="col-md-8 col-sm-6 col-xs-6 fvalue">'.$val.'</div>';
                        $data .= '</div>';
                    }
                }
                $value = $data;
                break;
            case 'Month':
                $value = \CustomHelper::date_format($value, 'month_save');

                break;
            case 'Multiselect':
                $data = "";
                if(isset($value) && is_array(json_decode($value))) {
                    $data .= '<ol>';
                    foreach(json_decode($value) as $val) {
                        $data .= '<li class="badge large bg-purple mr-1">'.$val.'</li>';
                    }
                    $data .= '</ol>';
                }
                $value = $data;
                break;
            case 'Phone':
                $value = '<a href="Tel:'.$value.'">'.$value.'</a>';
                break;
            case 'Polymorphic_select':
                if(isset($item->{$field_name.'_type'}) && class_exists($item->{$field_name.'_type'})) {
                    $ps_type = $item->{$field_name.'_type'};
                } else if(isset($item[$field_name.'_type']) && class_exists($item[$field_name.'_type'])) {
                    $ps_type = $item->{$field_name.'_type'};
                }
                if(isset($ps_type)) {
                    $ps_module = \Module::where('model',$item->{$field_name.'_type'})->first();
                    $ps_item = (new $item->{$field_name.'_type'})->find(($item->{$field_name.'_id'} ?? ""));
                    if(isset($ps_item->id) && isset($ps_module->id)) {
                        $data = (new $ps_module->model)->select(['id',$ps_module->represent_attr])->get();
                        $value = $ps_item->{$ps_module->represent_attr};
                    }
                }
                break;
            case 'Polymorphic_multiple':
                $data = "";
                $module = $fields[$field_name]->getJsonModule();
                if(isset($module->id)) {
                    $ralasion_field = $module->fields->firstWhere('name',($module->represent_attr ?? ""));
                    $polymorphic_module = $ralasion_field->getJsonModule();
                    $polymorphic_field = $module->fields->firstWhere('field_type.name','Polymorphic_select');
                    if(isset($polymorphic_module->id)) {
                        if(isset($item) && $item instanceof \Illuminate\Database\Eloquent\Model) {
                            $values = $item->morphToMany($polymorphic_module->model, $polymorphic_field->name,$module->table_name,null,$module->represent_attr)->where('attribute',$field_name)->get();
                            // \CustomHelper::ajprint($values);
                            foreach($values as $val) {
                                $data .= '<span class="badge large bg-purple mr-1">'.$val->{$polymorphic_module->represent_attr}.'</span>';
                            }
                        }
                    }
                }
                    
                $value = $data;
                break;
            case 'Radio':
            case 'Select':
            case 'Select2':
            case 'Select2_from_ajax':
                $json_values = null;
                if(isset($fields[$field_name]->json_values) || (is_array($fields[$field_name]) && isset($fields[$field_name]['json_values']))) {
                    $json_values = $fields[$field_name]->json_values ?? $fields[$field_name]['json_values'];
                }
                if(isset($json_values) && \Str::startsWith($json_values, "@")) {
                    $module = \Module::where('name',substr($json_values, 1))->first();
                    if(!isset($module->model)) {
                        $json_values_arr = explode('|',$json_values);
                        $module = (object)[];
                        $module->model = collect(str_replace("@", "", $json_values_arr))->first();
                        $module->table_name = (new $module->model)->getTable();
                        $module->represent_attr = collect(str_replace("|", "", $json_values_arr))->last();
                    }
                    $represent_attr = $module->represent_attr;
                    if(isset($value) && !empty($value)) {
                        $test_val = $module->model::find($value);
                        $value = \CustomHelper::get_represent_attr($test_val);
                    }
                }
                break;
            case 'Select2_multiple':
                $json_values = null;
                if(isset($fields[$field_name]->json_values) || (is_array($fields[$field_name]) && isset($fields[$field_name]['json_values']))) {
                    $json_values = $fields[$field_name]->json_values ?? $fields[$field_name]['json_values'];
                }
                $data = "";
                if(isset($json_values) && \Str::startsWith($json_values, "@")) {
                    if(isset($value) && is_array(json_decode($value))) {
                        foreach(json_decode($value) as $val) {
                            $module = \Module::where('name',substr($json_values, 1))->first();
                            if(!isset($module->model)) {
                                $json_values_arr = explode('|',$json_values);
                                $module = (object)[];
                                $module->model = collect(str_replace("@", "", $json_values_arr))->first();
                                $module->table_name = (new $module->model)->getTable();
                                $module->represent_attr = collect(str_replace("|", "", $json_values_arr))->last();
                            }
                            $represent_attr = $module->represent_attr;
                            $row_val = DB::table($module->table_name)->where('id', $val)->first();
                            
                            if(is_object($row_val)) {
                                $data .= '<span class="badge large bg-purple mr-1">'.$row_val->{$represent_attr}.'</span>';
                            } elseif(is_array($row_val)) {
                                $data .= '<span class="badge large bg-purple mr-1">'.$row_val[$represent_attr].'</span>';
                            }
                        }
                    }
                } else {
                    if(isset($value) && is_array(json_decode($value))) {
                        foreach(json_decode($value) as $val) {
                            $data .= '<span class="badge large bg-purple mr-1">'.$val.'</span>';
                        }
                    }
                }
                    
                $value = $data;
                break;
            case 'Select2_multiple_tags':
                $data = "";
                if(isset($value) && is_array(json_decode($value))) {
                    foreach(json_decode($value) as $val) {
                        $data .= '<span class="badge large bg-purple mr-1">'.$val.'</span>';
                    }
                } else {
                    $data .= '<span class="badge large bg-purple mr-1">'.$value.'</span>';
                }
                $value = $data;
                break;
            case 'Table':
                $data = '<table class="table">';
                if(isset($value) && is_array(json_decode($value))) {
                    $json_values = json_decode($fields[$field_name]->json_values);
                    if(is_array($json_values)) {
                        $data .= "<thead><tr>";
                        foreach($json_values as $prop ) {
                            $data .= '<th style="font-weight: 600!important;">'.$prop.'</th>';
                        }
                        $data .= "<th></th></tr></thead>";
                        foreach(json_decode($value) as $key => $val) {
                            $data .= "<tr class='array-row'>";
                                foreach( $json_values as $prop => $label) {
                                    $data .= "<td>".($val->{$label} ?? "")."</td>";
                                }
                            $data .= "</tr>";
                        }
                    }
                }
                $data .= "</table>";
                $value = $data;
                break;
            case 'Text':
            case 'Textarea':
            
                break;
            case 'Time':
                $value = \CustomHelper::date_format($value, 'field_show_time');

                break;
        }
        return $value;
    }

    /**
     * Print complete add/edit form for Module
     *
     * Uses blade syntax @display($employee_crud_object)
     *
     * @param $crud Module for which add/edit form has to be created.
     * @param array $fields List of Module Field Names to customize Selective Fields for Form
     * @return string returns HTML for complete Module Add/Edit Form
     */
    public static function displayAll($crud, $field_names = [], $input_attr = [], $only_required_field = false)
    {
        return self::form($crud, $field_names, $input_attr, 'display', $only_required_field);
    }

    /**
     * Print complete add/edit form for Module
     *
     * Uses blade syntax @form($employee_crud_object)
     *
     * @param $crud Module for which add/edit form has to be created.
     * @param array $fields List of Module Field Names to customize Selective Fields for Form
     * @return string returns HTML for complete Module Add/Edit Form
     */
    public static function form($crud, $field_names = [], $input_attr = [], $fuction = 'input', $only_required_field = false)
    {
        $fields = collect($crud->fields);
        if($fuction != 'display') {
            $fields = $fields->filter(function ($item, $key) {
                if((isset($item->field_type) && is_object($item->field_type) && isset($item->field_type->name) && $item->field_type->name == 'Hidden') || (isset($item->field_type) && is_string($item->field_type) && $item->field_type == 'Hidden') || (isset($item) && is_array($item) && is_string($item['field_type']) && $item['field_type'] == 'Hidden')) {
                    return false;
                } else {
                    return true;
                }
            });
        }
        if($only_required_field) {
            foreach($fields as $field) {
                if(isset($field->required) && $field->required == true) {
                    $field_names[] = $field->name;
                }
            }
        } else {
            $fields = $fields->keys()->toArray();
            // \CustomHelper::ajprint($fields);
            if(count($field_names) == 0) {
                $field_names = $fields;
            } else if(array_keys($field_names) !== range(0, count($field_names) - 1)) {
                if(isset($field_names->remove)){
                    $field_names = collect(array_diff($fields,$field_names->remove))->values();
                } else if(isset($field_names['remove'])){
                    $field_names = collect(array_diff($fields,$field_names['remove']))->values();
                } else if(isset($field_names->only)){
                    $field_names = collect($field_names->only)->values();
                } else if(isset($field_names['only'])){
                    $field_names = collect($field_names['only'])->values();
                }
            }
            // \CustomHelper::ajprint($field_names,false);
        }
        // \CustomHelper::ajprint($field_names,false);
        // echo "<pre>".json_encode($field_names,JSON_PRETTY_PRINT)."</pre>";exit;
        $out = '';
        $col = (isset($input_attr['col']) && $input_attr['col'] <= 12 ) ? $input_attr['col'] : 2;
        $col_class = "col-12 col-sm-".(($col > 0) ? 12/$col : 6);
        $field_names = collect($field_names)->chunk($col);
        foreach($field_names as $key => $field_parent) {
            if($fuction == "display") {
                $out .="<div class='list-group-item'>";
            }
            $out .= "<div class='row'>";
            foreach($field_parent as $key => $field) {
                if(isset($crud->fields[$field]->id)) {
                    $field_type_name = \Field::find($crud->fields[$field]->id)->field_type->name;
                } else if(isset($crud->fields[$field]->field_type)){
                    if(is_object($crud->fields[$field])) {
                        $field_type_name = $crud->fields[$field]->field_type;
                    } else {
                        $field_type_name = $crud->fields[$field]['field_type'];
                    }
                }
                
                if(isset($field_type_name)) {
                    $out .="<div class='".$col_class."'>";
                    if($fuction == "input") {
                        $arr = [];
                        $custom_input_attr = $input_attr['input_attr'] ?? [];
                        $arr_keys = array_keys($custom_input_attr);
                        if(in_array('all',$arr_keys)) {
                            $arr += $custom_input_attr['all'];
                        }
                        if(in_array($field,$arr_keys)) {
                            $arr += $custom_input_attr[$field];
                        }
                        $out .= self::input($crud, $field,$arr);
                    } else {
                        $out .= self::display($crud, $field);
                    }
                    $out .= "</div>";
                }
            }
            $out .= "</div>";
            if($fuction == "display") {
                $out .= "</div>";
            }
        }
        return $out;
    }
    
    /**
     * Check Whether User has Module Access
     * Work like @if blade directive of Laravel
     *
     * @param $crud_id Module Id for which Access will be checked
     * @param string $access_type Access type like - view / create / edit / delete
     * @param int $user_id User id for which access is checked. By default it takes logged-in user
     * @return bool return whether access for this Module is true / false
     */
    public static function access($crud_id, $access_type = "view", $user_id = 0)
    {
        return \Module::hasAccess($crud_id, $access_type, $user_id);
    }
    
    /**
     * Check Whether User has Module Access
     * Work like @if blade directive of Laravel
     *
     * @param $crud_id Module Id for which Access will be checked
     * @param string $access_type Access type like - view / create / edit / delete
     * @param int $user_id User id for which access is checked. By default it takes logged-in user
     * @return bool return whether access for this Module is true / false
     */
    public static function pageAccess($page_name, $access_type = "view", $user_id = 0)
    {
        // Check Module access by hasAccess method
        if(!class_exists(\App\Models\Page::class)) {
            $page = Page::where('name',$page_name)->first();
            return \Module::hasAccess($page, $access_type, $user_id);
        } else {
            return true;
        }
    }
    /**
     * Check Whether User has Module Field Access
     *
     * Work like @if blade directive of Laravel
     *
     * @param $crud_id Module Id for which Access will be checked
     * @param $field_id Field Id / Name for which Access will be checked
     * @param string $access_type Field Access type like - view / write
     * @param int $user_id User id for which access is checked. By default it takes logged-in user
     * @return bool return whether access for this Module Field is true / false
     */
    public static function field_access($crud_id, $field_id, $access_type = "view", $user_id = 0)
    {
        // Check Module Field access by hasFieldAccess method
        return \Module::hasFieldAccess($crud_id, $field_id, $access_type, $user_id);
    }
}