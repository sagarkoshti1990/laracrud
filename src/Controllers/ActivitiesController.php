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

class ActivitiesController extends Controller
{
    function __construct() {
        
        $this->crud = new \ObjectHelper;
        $module = (object)[];
        $module->name = "Activities";
        $module->label = "Activities";
        $module->table_name = "activity_log";
        $module->controller = "ActivitiesController";
        $module->represent_attr = "user_id";
        $module->icon = "fa-history";
        $module->model = "Activity";

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
            $data = view('inc.activities.logs_data',['activities' => $activities])->render();

            return response()->json(['status' => 'success', 'massage' => 'success', 'data' => $activities,'html' => $data]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(Module::hasAccess("Activities", "create")) {
            if(isset($request->src)) {
                $this->data['src'] = $request->src;
            }
            // prepare the fields you need to show
            $this->data['crud'] = $crud = $this->crud;
            $this->data['saveAction'] = $this->getSaveAction();
            $this->data['fields'] = $crud->getCreateFields();
            $this->data['title'] = trans('crud.add').' '.$crud->entity_name;

            return view('admin.Activities.create', $this->data);
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Module::hasAccess("Activities", "create")) {
            if (is_null($request)) {
                $request = \Request::instance();
            }

            $rules = Module::validateRules("Activities", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}

            // replace empty values with NULL, so that it will work with MySQL strict mode on
            foreach ($request->input() as $key => $value) {
                if (empty($value) && $value !== '0') {
                    $request->request->set($key, null);
                }
            }

            // insert item in the db
            $item = $this->crud->create($request->except(['save_action', '_token', '_method', 'src', 'src_ajax','go_view']));
            $this->data['entry'] = $this->crud->entry = $item;

            // show a success message
            if(!$request->src_ajax) {
                \Alert::success(trans('crud.insert_success'))->flash();
            }

            // save the redirect choice for next time
            $this->setSaveAction();

            if(isset($request->go_view) && $request->go_view) {
                return redirect($this->crud->route.'/'.$item->id);
            } else if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'massage' => 'updated']);
            } else if(isset($request->src)) {
                return redirect($request->src);
            } else {
                return $this->performSaveAction($item->getKey());
            }
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if(Module::hasAccess("Activities", "view")) {
            if(isset($request->src)) {
                $src = url($request->src);
            } else {
                $src = Null;
            }

            $activity = Activity::find($id);
            if(isset($activity->id)) {
                
                $crud = $this->crud;
                $crud->row = $activity;
            
                return view('admin.Activities.show', [
                    'crud' => $crud,
                    'activity' => $activity,
                    'src' => $src,
                    'title' => trans('crud.preview').' '.$crud->entity_name,
                    'show_col' => $crud->module->show_col,
                    'column_names' => $crud->column_names
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if(Module::hasAccess("Activities", "edit")) {
            if(isset($request->src)) {
                $src = $request->src;
            } else {
                $src = Null;
            }
            
            $activity = Activity::find($id);
            if(isset($activity->id)) {
                
                $crud = $this->crud;
                $crud->row = $activity;
            
                return view('admin.Activities.edit', [
                    'crud' => $crud,
                    'activity' => $activity,
                    'src' => $src,
                    'saveAction' => $this->getSaveAction(),
                    'title' => trans('crud.edit').' '.$crud->entity_name,
                    'entry' => $crud->getEntry($id)
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Module::hasAccess("Activities", "edit")) {
            if (is_null($request)) {
                $request = \Request::instance();
            }

            $rules = Module::validateRules("Activities", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}

            // replace empty values with NULL, so that it will work with MySQL strict mode on
            foreach ($request->input() as $key => $value) {
                if (empty($value) && $value !== '0') {
                    $request->request->set($key, null);
                }
            }

            // update the row in the db
            $item = $this->crud->update($id, $request->except('save_action', '_token', '_method', 'src', 'src_ajax'));
            $this->data['entry'] = $this->crud->entry = $item;

            // show a success message
            if(!$request->src_ajax) {
                \Alert::success(trans('crud.update_success'))->flash();
            }

            // save the redirect choice for next time
            $this->setSaveAction();

            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'massage' => 'updated']);
            } else if(isset($request->src)) {
                return redirect($request->src);
            } else {
                return $this->performSaveAction($item->getKey());
            }
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(Module::hasAccess("Activities", "delete")) {
            $activity = Activity::find($id)->delete();

            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'massage' => 'deleted']);
            } else if(isset($request->src)) {
                return redirect($request->src);
            } else {
                // return redirect()->route(config('lara.base.route_prefix') . 'crud.activities.index');
                return (string) $activity;
            }
        } else {
            abort(403, trans('crud.unauthorized_access'));
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
        // $collection = collect(['id']);
        // $merged = $collection->merge($this->crud->column_names);
        // $listing_cols = $merged->all();
        $listing_cols = ['id','user_id','context_id','action','description'];
        
        if(isset($request->filter)) {
			$values = DB::table('activity_log')->select($listing_cols)->where($request->filter)->orderBy('id', 'desc');
		} else {
			$values = DB::table('activity_log')->select($listing_cols)->orderBy('id', 'desc');
		}
        
        $out = Datatables::of($values)->make();
        $data = $out->getData();
        
        // $fields_popup = Field::getFields('Activities');
        
        for($i = 0; $i < count($data->data); $i++) {
            $data->data[$i] = collect($data->data[$i])->values()->all();
            $activity = Activity::find($data->data[$i][0]);

            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];

                if(isset($data->data[$i][$j]) && $data->data[$i][$j]) {
                    if($col == "user_id") {
                        $user = \App\User::find($data->data[$i][$j]);
                        if(isset($user->context()->id)) {
                            $data->data[$i][$j] = '<a href="' . url(config('lara.base.route_prefix') .'/employees/'. $user->context()->id) . '">' . $user->context()->name() . '</a>';
                        }
                    } else if($col == "context_id") {
                        $data->data[$i][$j] = $activity->getUrl();
                    }
                } else {
                    // $data->data[$i][$j] = $col;
                }
            }

            // button
            $output = '<i id="'.$activity->id.'" class="fa fa-plus-circle details-control"></i>';
            $data->data[$i][] = (string)$output;
        }
        $out->setData($data);
        return $out;
    }
}
