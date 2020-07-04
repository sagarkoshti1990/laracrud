<?php

namespace Sagartakle\Laracrud\Helpers;

use Sagartakle\Laracrud\Helpers\Traits\Create;
use Sagartakle\Laracrud\Helpers\Traits\Update;
use Sagartakle\Laracrud\Helpers\Traits\Delete;
use Sagartakle\Laracrud\Helpers\Traits\Fields;
use Sagartakle\Laracrud\Helpers\Traits\Access;

class ObjectHelper
{
    use Create;
    use Update;
    use Delete;
    use Fields;
    use Access;
    // --------------
    // CRUD Object
    // --------------
    
    public $module = [];
    public $model;
    public $table_name;
    public $controller;
    public $represent_attr;
    public $icon;
    public $route;
    public $access = [];
    public $columns = [];
    public $column_names = [];
    public $fields = [];
    public $entry;
    public $buttons;
    public $label;
    public $name;
    public $labelPlural;
    public $view_path = [];

    /**
     * This function binds the CRUD to its corresponding Model (which extends Eloquent).
     * All Create-Read-Update-Delete operations are done using that Eloquent Collection.
     *
     * @param [string] Full model namespace. Ex: App\Models\Article
     */
    public function setModel($model_namespace)
    {
        if (! class_exists($model_namespace)) {
            throw new \Exception($model_namespace.' This model does not exist. from '.$this->name, 404);
        }

        $this->model = new $model_namespace();
        $this->initButtons();
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
     * set module info; 
     *
     * @return [Eloquent Collection]
     */
    public function setModule($module)
    {
        $this->module = clone $module;

        $this->table_name = $module->table_name;
        $this->name = $module->name;
        $this->controller = $module->controller;
        $this->represent_attr = $module->represent_attr;
        $this->icon = $module->icon;
        if(isset($module->fields)) {
            $this->setFields($module->fields);
        }
        $this->setViewPath([
            'index' => config('stlc.stlc_modules_folder_name','stlc::').'index',
            'create' => config('stlc.stlc_modules_folder_name','stlc::').'form',
            'edit' => config('stlc.stlc_modules_folder_name','stlc::').'form',
            'show' => config('stlc.stlc_modules_folder_name','stlc::').'show',
        ]);
        $this->setModel($module->model);
        $this->setColumnNames($module->table_name);

        $this->setRoute(config('stlc.route_prefix') . '/'.$module->table_name);
        if(isset($module->fields)) {
            $this->setColumns($module->fields);
        }

        $this->setEntityNameStrings(\Str::singular($module->label),$module->label);
    }
    
    /**
     * Get the number of rows that should be show on the table page (list view).
     */
    public function getDefaultPageLength()
    {
        // return the custom value for this crud panel, if set using setPageLength()
        // if ($this->default_page_length) {
        //     return $this->default_page_length;
        // }

        // otherwise return the default value in the config file
        if (config('stlc.default_page_length')) {
            return config('stlc.default_page_length');
        }

        return 25;
    }

    /**
     * set fields; 
     *
     * @return [Eloquent Collection]
     */
    public function setViewPath($view_path)
    {
        $this->view_path = $view_path;
    }
    /**
     * set fields; 
     *
     * @return [Eloquent Collection]
     */
    public function setFields($fields)
    {
        foreach ($fields as $key => $value) {
            if(!is_object($value)) {
                $value = (object) $value;
            }
            $this->fields[$value->name] = $value;
        }
    }
    
    /**
     * set columns; 
     *
     * @return [Eloquent Collection]
     */
    public function setColumns($fields,$only = [])
    {
        $this->columns = [];
        foreach ($fields as $key => $value) {
            if(!is_object($value)) {
                $type = $value['field_type'];
                $value = (object) $value;
            } else {
                $type = strtolower($value->field_type->name);
            }
            if((isset($value->show_index) && $value->show_index && count($only) == 0) || in_array($value->name,$only)) {
                $this->columns[$value->name] = [
                    'name'  => $value->name,
                    'label' => $value->label,
                    'type'  => $type,
                ];
            }
        }
    }
    
    /**
     * Add a button to the CRUD table view auto.
     */    
    public function initButtons()
    {
        $this->buttons = collect();

        // line stack
        // $this->addButton('line', 'preview', 'view', 'stlc::buttons.preview', 'end');
        $this->addButton('line', 'clone', 'view', 'stlc::buttons.clone', 'end');
        $this->addButton('line', 'update', 'view', 'stlc::buttons.update', 'end');
        $this->addButton('line', 'delete', 'view', 'stlc::buttons.delete', 'end');
        $this->addButton('line', 'restore', 'view', 'stlc::buttons.restore', 'end');

        // top stack
        $this->addButton('top', 'create', 'view', 'stlc::buttons.create');
        // $this->addButton('top', 'deleted_data', 'view', 'stlc::buttons.deleted_data');
    }

    /**
     * Add a button to the CRUD table view.
     */
    public function addButton($stack, $name, $type, $content, $position = false)
    {
        if ($position == false) {
            switch ($stack) {
                case 'line':
                    $position = 'beginning';
                    break;

                default:
                    $position = 'end';
                    break;
            }
        }

        switch ($position) {
            case 'beginning':
                $this->buttons->prepend((object)['stack' => $stack, "name" => $name, "type" => $type, "content" => $content]);
                break;

            default:
                $this->buttons->push((object)['stack' => $stack, "name" => $name, "type" => $type, "content" => $content]);
                break;
        }
    }

    public function removeButton($name)
    {
        $this->buttons = $this->buttons->reject(function ($button) use ($name) {
            return $button->name == $name;
        });
    }

    public function onlyButton($name)
    {
        if(!is_array($name)) {
            $name = [$name];
        }
        // echo json_encode($this->buttons);
        $this->buttons = $this->buttons->whereIn('name', $name)->values();
    }

    public function removeAllButtons()
    {
        $this->buttons = collect([]);
    }

    public function isColumnNullable($column_name)
    {
        // create an instance of the model to be able to get the table name
        $instance = $this->model;

        $conn = \DB::connection($instance->getConnectionName());
        $table = \Config::get('database.connections.'.env('DB_CONNECTION').'.prefix').$instance->getTable();

        // register the enum column type, because Doctrine doesn't support it
        $conn->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        return ! $conn->getDoctrineColumn($table, $column_name)->getNotnull();
    }

    /**
     * get module info;
     *
     * @return [Eloquent Collection]
     */
    public function getModule()
    {
        return $this->module;
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
    
    /**
     * Get the current CrudController route.
     *
     * Can be defined in the CrudController with:
     *
     * @return [string]
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the entity name in singular and plural.
     * Used all over the CRUD interface (header, add button, reorder button, breadcrumbs).
     *
     * @param [string] Entity name, in singular. Ex: article
     * @param [string] Entity name, in plural. Ex: articles
     */
    public function setEntityNameStrings($singular, $plural)
    {
        $this->label = trim($singular);
        $this->labelPlural = trim($plural);
    }

    // ----------------------------------
    // Miscellaneous functions or methods
    // ----------------------------------

    /**
     * Return the first element in an array that has the given 'type' attribute.
     *
     * @param string $type
     * @param array  $array
     *
     * @return array
     */
    public function getFirstOfItsTypeInArray($type, $array)
    {
        return array_first($array, function ($item) use ($type) {
            if(isset($item['field_type_id']) && isset($item['field_type_id'])) {
                return strtolower($item['field_type']->name) == $type;
            } else {
                return $item['type'] == $type;
            }
        });
    }

    // ------------
    // TONE FUNCTIONS - UNDOCUMENTED, UNTESTED, SOME MAY BE USED IN THIS FILE
    // ------------
    //
    // TODO:
    // - figure out if they are really needed
    // - comments inside the function to explain how they work
    // - write docblock for them
    // - place in the correct section above (CREATE, READ, UPDATE, DELETE, ACCESS, MANIPULATION)

    public function sync($type, $fields, $attributes)
    {
        if (! empty($this->{$type})) {
            $this->{$type} = array_map(function ($field) use ($fields, $attributes) {
                if (in_array($field['name'], (array) $fields)) {
                    $field = array_merge($field, $attributes);
                }

                return $field;
            }, $this->{$type});
        }
    }

    public function setColumnNames($table, $option="")
    {
        $arr = collect($this->fields)->where('field_type.name','!=','Polymorphic_select')->where('field_type.name','!=','Polymorphic_multiple')->keys()->toArray();
        if(isset($option) && $option == "All") {
            $this->column_names = $arr;
        } else if(is_array($option) && count($option)) {
            $this->column_names = collect($arr)->intersect($option)->all();
        } else {
            $this->column_names = collect($arr)->diff(['id', 'created_at', 'updated_at', 'deleted_at'])->all();
        }
        // if($this->fields->where(''))
        $fields = collect($this->fields)->where('field_type.name', 'Polymorphic_select')->keys()->toArray();
        foreach($fields as $field) {
            $this->column_names[] = $field.'_id';
            $this->column_names[] = $field.'_type';
        }
    }

    public function setColumnLabel($column_name, $label)
    {
        $this->columns[$column_name]['label'] = $label;
    }
}
