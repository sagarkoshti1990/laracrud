<?php

namespace Sagartakle\Laracrud\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use DB;
use Validator;
use Yajra\DataTables\Datatables;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\Field;
use Collective\Html\FormFacade as Form;
use Sagartakle\Laracrud\Models\Activity;
use Sagartakle\Laracrud\Helpers\ObjectHelper;

class ActivitiesController extends Controller
{
    function __construct() {
        
        $this->crud = new ObjectHelper;
        $module = (object)[];
        $module->name = "Activities";
        $module->label = "Activities";
        $module->table_name = "activity_log";
        $module->controller = "ActivitiesController";
        $module->represent_attr = "user_id";
        $module->icon = "fa-history";
        $module->model = Activity::class;

        $module->fields = [
            [
				'name' => 'user_id',
				'label' => 'Name',
				'field_type' => 'Text',
				'unique' => false,
				'defaultvalue' => Null,
				'minlength' => '0',
				'maxlength' => '0',
				'required' => true,
				'show_index' => true
			],[
				'name' => 'context_id',
				'label' => 'label',
				'field_type' => 'Text',
				'unique' => false,
				'defaultvalue' => Null,
				'minlength' => '0',
				'maxlength' => '0',
				'required' => true,
				'show_index' => true
			],[
				'name' => 'action',
				'label' => 'Table Name',
				'field_type' => 'Text',
				'unique' => false,
				'defaultvalue' => Null,
				'minlength' => '0',
				'maxlength' => '0',
				'required' => true,
				'show_index' => true
            ],[
				'name' => 'description',
				'label' => 'Table Name',
				'field_type' => 'Text',
				'unique' => false,
				'defaultvalue' => Null,
				'minlength' => '0',
				'maxlength' => '0',
				'required' => true,
				'show_index' => true
            ]
        ];

        $this->crud->setModule($module);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $crud = $this->crud;
		
		if(\Auth::user()->isSuperAdmin()) {
            
            $activities = Activity::all();
            // echo "<pre>".json_encode($crud,JSON_PRETTY_PRINT);exit;
            $this->data['crud'] = $crud;
            $this->data['title'] = ucfirst($crud->labelPlural);

            // get all entries if AJAX is not enabled
            // if (! $this->data['crud']->ajaxTable()) {
            //     $this->data['entries'] = $activities;
            // }

            return view('admin.Activities.index', $this->data);
        } else {
            abort(403, trans('crud.unauthorized_access'));
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
            return response()->json(['status' => 'validation_error', 'massage' => 'created', 'errors' => $validator->errors()]);
        } else {
            $modal = $request->context_type;
            $item = $modal::find($request->context_id);
            $activities = $item->activities()->orderBy('created_at', 'desc')->paginate(10);
            $data = view(config('stlc.stlc_modules_folder_name','stlc::').'inc.activities.logs_data',['activities' => $activities])->render();

            return response()->json(['status' => 'success', 'massage' => 'success', 'data' => $activities,'html' => $data]);
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
        if(Module::hasAccess("Activities", "view")) {
            if(isset($request->src)) {
                $src = url($request->src);
            } else {
                $src = Null;
            }

            $activity = Activity::find($id);
            if(isset($activity->id)) {
                
                return response()->json([
                    'activity' => $activity,
                    'status' => 'success',
                    'massage' => 'updated'
                ]);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("activities"),
                ]);
            }
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }
}
