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
    public $view_atrributes = [];

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
        if(isset($module->fields) && is_array($this->fields) && count($this->fields) < 1) {
            $this->setFields($module->fields);
        }
        if(!isset($this->view_path) || (is_array($this->view_path) && count($this->view_path) == 0)) {
            $this->setViewPath([
                'index' => config('stlc.view_path.index','stlc::index'),
                'create' => config('stlc.view_path.create','stlc::form'),
                'edit' => config('stlc.view_path.edit','stlc::form'),
                'show' => config('stlc.view_path.show','stlc::show'),
            ]);
        }
        if(!isset($this->model)) {
            $this->setModel($module->model);
        }
        if(!isset($this->column_names) || (is_array($this->column_names) && count($this->column_names) == 0)) {
            $this->setColumnNames($module->table_name);
        }
        if(!isset($this->route)) {
            $this->setRoute(config('stlc.route_prefix') . '/'.$module->table_name);
        }
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
    public function setFields($fields,$only = [])
    {
        $this->fields = [];
        if(isset($only) && (is_array($only) || is_object($only)) && count($only) > 0) {
            foreach($only as $field_name) {
                $value = collect($fields)->where('name',$field_name)->first();
                if(!is_object($value)) {
                    $value = (object) $value;
                }
                $this->fields[$value->name] = $value;
            }
        } else {
            foreach ($fields as $key => $value) {
                if(!is_object($value)) {
                    $value = (object) $value;
                }
                $this->fields[$value->name] = $value;
            }
        }
    }

    public function addFields($fields)
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
     * $stack, $name, $type, $content, $position = false
     * Add a button to the CRUD table view auto.
     */    
    public function initButtons()
    {
        $this->buttons = collect();
        $btnArr = [];
        $defult = [
            ['stack' => 'line', 'name' => 'preview', 'type' => 'view', 'content' => 'stlc::buttons.preview'],
            ['stack' => 'line', 'name' => 'clone', 'type' => 'view', 'content' => 'stlc::buttons.clone'],
            ['stack' => 'line', 'name' => 'edit', 'type' => 'view', 'content' => 'stlc::buttons.edit'],
            ['stack' => 'line', 'name' => 'delete', 'type' => 'view', 'content' => 'stlc::buttons.delete'],
            ['stack' => 'line', 'name' => 'restore', 'type' => 'deleted', 'content' => 'stlc::buttons.restore'],
            ['stack' => 'line', 'name' => 'permanently_delete', 'type' => 'deleted', 'content' => 'stlc::buttons.permanently_delete'],
            ['stack' => 'top', 'name' => 'create', 'type' => 'view', 'content' => 'stlc::buttons.create']
        ];
        $buttons = config('stlc.buttons',[]);
        
        foreach($buttons as $button) {
            $btnArr[] = $button;
        }
        foreach($defult as $button) {
            if(config('stlc.buttons.'.$button['name'],true) === true) {
                $btnArr[] = $button;
            }
        }
        foreach($btnArr as $button) {
            if($button) {
                $this->addButton($button);
            }
        }
    }

    /**
     * Add a button to the CRUD table view.
     */
    public function addButton($data)
    {
        switch ($data['position'] ?? '') {
            case 'beginning':
                $this->buttons->prepend((object)$data);
                break;

            default:
                $this->buttons->push((object)$data);
                break;
        }
    }

    public function removeButton($name)
    {
        $this->buttons = $this->buttons->reject(function ($button) use ($name) {
            return isset($button->name) && $button->name == $name;
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
     * Used all over the CRUD interface (header, add button, breadcrumbs).
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
        $arr = collect($this->fields)->whereNotIn('field_type.name',['Polymorphic_select','Polymorphic_multiple','Files'])->keys()->toArray();
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

    public function getViewAtrributes($path,$updateAtribute=[])
    {
        $arr = \Arr::get($this->view_atrributes,$path,[]);
        if(is_array($arr) && count($arr) == 0) {
            if(\Str::contains($path,['index.','form.','show.'])) {
                $arr = \Arr::get($this->view_atrributes,str_replace(['index.','form.','show.'],"",$path),[]);
            }
        }

        if(is_array($arr) && count($arr) == 0) {
            $arr = config('stlc.view.attributes.'.$this->name.'.'.$path,[]);
            if(is_array($arr) && count($arr) == 0) {
                if(\Str::contains($path,['index.','form.','show.'])) {
                    $arr = config('stlc.view.attributes.'.$this->name.'.'.str_replace(['index.','form.','show.'],"",$path),[]);
                }
            }
        }
        
        if(is_array($arr) && count($arr) == 0) {
            $arr = config('stlc.view.attributes.'.$path,[]);
            if(is_array($arr) && count($arr) == 0) {
                if(\Str::contains($path,['index.','form.','show.'])) {
                    $arr = config('stlc.view.attributes.'.str_replace(['index.','form.','show.'],"",$path),[]);
                }
            }
        }
        
        if(is_string($arr)) {
            $arr = ['string' => $arr];
        }
        return array_replace($updateAtribute,$arr);
    }
}
