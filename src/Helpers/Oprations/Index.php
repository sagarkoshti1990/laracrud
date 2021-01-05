<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Datatables;

trait Index
{
    public function changeDataIndex(Request $request) : Array
    {
        $filters = [];
        foreach($request->all() as $key => $value) {
            if(in_array($key,$this->crud->column_names) || $key == 'id') {
                $filters[$this->crud->table_name.'.'.$key] = $value;
            }
        }
        return [
            'crud' => $this->crud,
            'filters' => $filters
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($this->crud->hasAccess('view')) {
            if($request->wantsJson()) {
                $query = $this->crud->model;
                if(isset($request->q)) {
                    $field_names = $this->crud->column_names;
                    foreach($field_names as $value) {
                        if(in_array($value,config('stlc.represent_attr.'.$this->crud->name,[$this->crud->represent_attr]))) {
                            $query = $query->orWhere($value,'LIKE',"%".$request->q."%");
                        }
                    }
                }
                $result = $query->paginate(10);
                foreach($result as $value) {
                    $arr[] = ['text'=>\CustomHelper::get_represent_attr($value),'value'=>$value->id];
                }
                return response()->json(['status' => '200', 'message' => 'success', 'item' => $arr ?? []]);
            } else {
                return view($this->crud->view_path['index'], $this->changeDataIndex($request));
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
     * queryDatatable
    */
    public function queryDatatable(Request $request)
    {
        $listing_cols = \Module::getListingColumns($this->crud);
        $values = DB::table($this->crud->table_name)->select($listing_cols)->latest();
        if(isset($request->filter)) {
			$values->where($request->filter);
        }
        return $values;
    }
    /**
     * changeDatatable
    */
    public function changeDatatable(Array $data,Array $listing_cols,$item)
    {
        for($j = 0; $j < count($listing_cols); $j++) {
            $col = $listing_cols[$j];
            $data[$j] = \FormBuilder::get_field_value($this->crud, $col);
            if(isset($data[$j]) && $col == $this->crud->module->represent_attr && !isset($item->deleted_at)) {
                $data[$j] = '<a href="' . url($this->crud->route .'/'. $item->id) . '"><i class="'.config('stlc.view.icon.button.preview','fa fa-eye').' mr-1"></i>' . \CustomHelper::get_represent_attr($item). '</a>';
            }
        }
        return $data;
    }
    /**
     * actionBtnDatatable
    */
    public function actionBtnDatatable($item)
    {
        return \View::make(config('stlc.view_path.inc.button_stack','stlc::inc.button_stack'), ['stack' => 'line','from_view'=>'index'])
                ->with('crud', $this->crud)
                ->with('entry', $item)
                ->render();
    }
    /**
     * on Datatable fetch via Ajax
    */
    public function onDatatable($data){
        // array_splice($listing_cols, 2, 0, "index_name");
        for($i = 0; $i < count($data); $i++) {
            $collectuser = collect($data[$i]);
            $data[$i] = $collectuser->values()->all();
            $this->crud->row = $item = $this->crud->model->withTrashed()->find($data[$i][0]);
            // array_splice($data[$i], 2, 0, true);
            $data[$i] = $this->changeDatatable($data[$i],$collectuser->keys()->all(),$item);
            if ($this->crud->buttons->where('stack', 'line')->count()) {
                $data[$i][] = $this->actionBtnDatatable($item);
            }
        }
        return $data;
    }
    /**
     * Server side Datatable fetch via Ajax
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $out = Datatables::of($this->queryDatatable($request))->make();
        $data = $out->getData();
        $data->data = $this->onDatatable($data->data);
        return $out->setData($data);
    }
} 