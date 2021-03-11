<?php

namespace Sagartakle\Laracrud\Helpers;

use Route;

class RouteHelper
{
    protected $extraRoutes = [];

    protected $name = null;
    protected $options = null;
    protected $controller = null;

    public function __construct($name, $controller, $options)
    {
        $this->name = $name;
        $this->controller = $controller;
        $this->options = $options;
        $this->route_list = ['datatable','restore','permanently_delete'];
        if(isset($options['only']) && is_array($options['only'])) {
            $this->route_list = collect($this->route_list)->intersect($options['only'])->toArray();
        }
        if(isset($options['except']) && is_array($options['except'])) {
            $this->route_list = collect($this->route_list)->diff($options['except'])->toArray();
        }

        $this->namePrefix = $options['namePrefix'] ?? 'crud';
    }

    /**
     * The CRUD resource needs to be registered after all the other routes.
     */
    public function __destruct()
    {
        $options_with_default_route_names = array_merge([
            'names' => [
                'index'     => $this->namePrefix.'.'.$this->name.'.index',
                'create'    => $this->namePrefix.'.'.$this->name.'.create',
                'store'     => $this->namePrefix.'.'.$this->name.'.store',
                'edit'      => $this->namePrefix.'.'.$this->name.'.edit',
                'update'    => $this->namePrefix.'.'.$this->name.'.update',
                'show'      => $this->namePrefix.'.'.$this->name.'.show',
                'destroy'   => $this->namePrefix.'.'.$this->name.'.destroy',
            ],
        ], $this->options);

        if(in_array('datatable',$this->route_list)) {
            Route::post($this->name.'/datatable', [
                'as' => $this->namePrefix.'.'.$this->name.'.datatable',
                'uses' => $this->controller.'@datatable',
            ]);
        }
        
        if(in_array('restore',$this->route_list)) {
            Route::Post($this->name.'/{id}/restore', [
                'as' => $this->namePrefix.'.'.$this->name.'.restore',
                'uses' => $this->controller.'@restore',
            ]);
        }
        
        if(in_array('permanently_delete',$this->route_list)) {
            Route::Post($this->name.'/{id}/permanently_delete', [
                'as' => $this->namePrefix.'.'.$this->name.'.permanently_delete',
                'uses' => $this->controller.'@permanently_delete',
            ]);
        }
        Route::resource($this->name, $this->controller, $options_with_default_route_names);
    }

    public function __call($method, $parameters = null)
    {
        if (method_exists($this, $method)) {
            $this->{$method}($parameters);
        }
    }
    
    public static function resource($name, $controller, array $options = [])
    {
        return new RouteHelper($name, $controller, $options);
    }
}
