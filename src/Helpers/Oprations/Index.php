<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Sagartakle\Laracrud\Models\Field;
use Sagartakle\Laracrud\Models\Module;
use Illuminate\Http\Request;
use Sagartakle\Laracrud\Helpers\FormBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Datatables;

trait Index
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($this->crud->hasAccess('view')) {
            $crud = $this->crud;
            if($request->wantsJson()) {
                $query = $crud->model;
                if(isset($request->q)) {
                    $query = $query->where($crud->represent_attr,'LIKE',"%".$request->q."%");
                }
                return response()->json(['status' => '200', 'message' => 'success', 'item' => $query->paginate(10)]);
            } else {
                return view($crud->view_path['index'], ['crud' => $crud]);
            }
        } else {
            if($request->wantsJson()) {
                return response()->json(['status' => '403', 'message' => trans('stlc.unauthorized_access')],403);
            } else {
                abort(403, trans('stlc.unauthorized_access'));
            }
        }
    }

    /**
     * Server side Datatable fetch via Ajax
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $crud = $this->crud;
        $listing_cols = config('stlc.module_model')::getListingColumns($crud);
        $values = DB::table($crud->table_name)->select($listing_cols)->latest();
        if(isset($request->filter)) {
			$values->where($request->filter);
		}
        
        $out = Datatables::of($values)->make();
        $data = $out->getData();
        $fields_popup = config('stlc.field_model')::getFields($crud->name);
        // array_splice($listing_cols, 2, 0, "index_name");
        
        for($i = 0; $i < count($data->data); $i++) {
            $collectuser = collect($data->data[$i]);
            $listing_cols = $collectuser->keys()->all();
            $data->data[$i] = $collectuser->values()->all();
            // \CustomHelper::ajprint($collectuser);
            $crud->row = $item = $crud->model->withTrashed()->find($data->data[$i][0]);
            // array_splice($data->data[$i], 2, 0, true);
            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];
                $data->data[$i][$j] = FormBuilder::get_field_value($crud, $col);
                if(isset($data->data[$i][$j]) && $col == $crud->module->represent_attr && !isset($item->deleted_at)) {
                    $data->data[$i][$j] = '<a href="' . url($crud->route .'/'. $item->id) . '"><i class="fa fa-eye mr-1"></i>' . $data->data[$i][$j] . '</a>';
                }
            }
            
            if ($crud->buttons->where('stack', 'line')->count()) {
                $crud->datatable = true;
                $output = '';
                
                $output .= \View::make(config('stlc.stlc_modules_folder_name','stlc::').'inc.button_stack', ['stack' => 'line'])
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