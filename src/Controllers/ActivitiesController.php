<?php

namespace Sagartakle\Laracrud\Controllers;

use Sagartakle\Laracrud\Controllers\StlcController;
use Illuminate\Http\Request;

use DB;
use Validator;
use Yajra\DataTables\Datatables;

class ActivitiesController extends StlcController
{
    function __construct() {
        
        $this->crud = new \ObjectHelper;
        $module = (object)[];
        $module->name = "Activities";
        $module->label = "Activities";
        $module->table_name = "activity_log";
        $module->controller = "ActivitiesController";
        $module->represent_attr = "user_id";
        $module->icon = "fa fa-history";
        $module->model = \Activity::class;

        $module->fields = [
            [
				'name' => 'user_id',
				'label' => 'Name',
				'field_type' => 'Select2',
				'required' => true,
                'show_index' => true,
                'json_values' => '@Users'
			],[
				'name' => 'context',
				'label' => 'Module',
				'field_type' => 'Polymorphic_select',
				'required' => true,
				'show_index' => true
			],[
				'name' => 'action',
				'label' => 'Action',
				'field_type' => 'Text',
				'required' => true,
				'show_index' => true
            ],[
				'name' => 'description',
				'label' => 'Description',
				'field_type' => 'Textarea',
				'required' => true,
				'show_index' => true
            ]
        ];

        $this->crud->setModule($module);
        $this->crud->setRoute(config('stlc.stlc_route_prefix', 'developer').'/'.$module->table_name);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $crud = $this->crud;
		if(\Module::user()->isSuperAdmin()) {
            $activities = \Activity::all();
            $this->data['crud'] = $crud;
            $this->data['title'] = ucfirst($crud->labelPlural);
            return view(config('stlc.view_path.Activities.index','stlc::Activities.index'), $this->data);
        } else {
            abort(403, trans('stlc.unauthorized_access'));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data_ajax(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'context_type' => 'required',
            'context_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => 'validation_error', 'message' => 'created', 'errors' => $validator->errors()]);
        } else {
            $modal = $request->context_type;
            $item = $modal::find($request->context_id);
            $activities = $item->activities()->orderBy('created_at', 'desc')->paginate(10);
            $data = view(config('stlc.view_path.inc.activities.logs_data','stlc::inc.activities.logs_data'),['activities' => $activities])->render();

            return response()->json(['status' => 'success', 'message' => 'success', 'data' => $activities,'html' => $data]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_data(Request $request, $id)
    {
        if($this->crud->hasAccess('view')) {
            if(isset($request->src)) {
                $src = url($request->src);
            } else {
                $src = Null;
            }

            $activity = \Activity::find($id);
            if(isset($activity->id)) {
                
                return response()->json([
                    'activity' => $activity,
                    'status' => 'success',
                    'message' => 'updated'
                ]);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("activities"),
                ]);
            }
        } else {
            abort(403, trans('stlc.unauthorized_access'));
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
        $listing_cols = ['id','user_id','context_id','action','description'];
        $values = DB::table($crud->table_name)->select($listing_cols)->latest();
        if(isset($request->filter)) {
			$values->where($request->filter);
		}
        
        $out = Datatables::of($values)->make();
        $data = $out->getData();
        // array_splice($listing_cols, 2, 0, "index_name");
        
        for($i = 0; $i < count($data->data); $i++) {
            $collectuser = collect($data->data[$i]);
            $listing_cols = $collectuser->keys()->all();
            $data->data[$i] = $collectuser->values()->all();
            // \CustomHelper::ajprint($collectuser);
            $crud->row = $item = $crud->model->find($data->data[$i][0]);
            // array_splice($data->data[$i], 2, 0, true);
            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];
                $data->data[$i][$j] = \FormBuilder::get_field_value($crud, $col);
                if(isset($data->data[$i][$j]) && $col == $crud->module->represent_attr && !isset($item->deleted_at)) {
                    $data->data[$i][$j] = '<a href="' . url($crud->route .'/'. $item->id) . '">' . $data->data[$i][$j] . '</a>';
                }
            }

            // button
            $output = '<i id="'.$item->id.'" class="fa fa-plus-circle details-control"></i>';
            $data->data[$i][] = (string)$output;
        }
        $out->setData($data);
        return $out;
    }
}
