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

        Route::post($this->name.'/datatable', [
            'as' => 'crud.'.$this->name.'.datatable',
            'uses' => $this->controller.'@datatable',
        ]);
        
        Route::Post($this->name.'/{id}/restore', [
            'as' => 'crud.'.$this->name.'.restore',
            'uses' => $this->controller.'@restore',
        ]);
        
        Route::Post($this->name.'/{id}/permanently_delete', [
            'as' => 'crud.'.$this->name.'.permanently_delete',
            'uses' => $this->controller.'@permanently_delete',
        ]);
    }

    /**
     * The CRUD resource needs to be registered after all the other routes.
     */
    public function __destruct()
    {
        $options_with_default_route_names = array_merge([
            'names' => [
                'index'     => 'crud.'.$this->name.'.index',
                'create'    => 'crud.'.$this->name.'.create',
                'store'     => 'crud.'.$this->name.'.store',
                'edit'      => 'crud.'.$this->name.'.edit',
                'update'    => 'crud.'.$this->name.'.update',
                'show'      => 'crud.'.$this->name.'.show',
                'destroy'   => 'crud.'.$this->name.'.destroy',
            ],
        ], $this->options);

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
