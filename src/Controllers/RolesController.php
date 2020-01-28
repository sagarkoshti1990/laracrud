<?php

namespace Sagartakle\Laracrud\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sagartakle\Laracrud\Models\Role;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Controllers\StlcController;
use Yajra\DataTables\Datatables;
use Sagartakle\Laracrud\Models\Field;

class RolesController extends StlcController
{
    function __construct() {
        $this->crud = Module::make('Roles');
        $this->crud->setRoute(config('stlc.stlc_route_prefix', 'developer'). '/'.$this->crud->table_name);
    }
    
    // write custom function or override function.

    /**
     * Server side Datatable fetch via Ajax
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $crud = $this->crud;
        $listing_cols = Module::getListingColumns($this->crud->name);
        
        if(isset($request->filter)) {
			$values = DB::table($this->crud->table_name)->select($listing_cols)->whereNull('deleted_at')->where('parent_id', '!=', null)->where($request->filter);
		} else {
			$values = DB::table($this->crud->table_name)->select($listing_cols)->where('parent_id', '!=', null)->whereNull('deleted_at');
		}
        
        $out = Datatables::of($values)->make();
        $data = $out->getData();
        
        $fields_popup = Field::getFields($this->crud->name);
        
        // array_splice($listing_cols, 2, 0, "index_name");
        
        for($i = 0; $i < count($data->data); $i++) {
            $data->data[$i] = collect($data->data[$i])->values()->all();
            $item = $this->crud->model->find($data->data[$i][0]);
            // array_splice($data->data[$i], 2, 0, true);
            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];
                if(isset($data->data[$i][$j]) && $data->data[$i][$j]) {
                    if(isset($fields_popup[$col])) {
                        $data->data[$i][$j] = \FormBuilder::get_field_value($crud, $col, $item->$col);
                    }
                    if($col == $crud->module->represent_attr && !isset($item->deleted_at)) {
                        $data->data[$i][$j] = '<a href="' . url($crud->route .'/'. $item->id) . '">' . $data->data[$i][$j] . '</a>';
                    }
                }
            }
            
            if ($crud->buttons->where('stack', 'line')->count()) {
                $crud->datatable = true;
                $output = '';
                
                $output .= \View::make('crud.inc.button_stack', ['stack' => 'line'])
                ->with('crud', $crud)
                ->with('entry', $item)
                ->render();

                $data->data[$i][] = (string)$output;
            }
        }
        $out->setData($data);
        return $out;
    }
}
