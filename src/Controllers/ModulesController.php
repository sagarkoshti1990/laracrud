<?php

namespace Sagartakle\Laracrud\Controllers;

use Sagartakle\Laracrud\Controllers\StlcController;
use Illuminate\Http\Request;

use Auth;
use DB;
use Validator;
use Yajra\DataTables\Datatables;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Helpers\ObjectHelper;
use Prologue\Alerts\Facades\Alert;
use Sagartakle\Laracrud\Models\Field;
use Sagartakle\Laracrud\Models\FieldType;
use Sagartakle\Laracrud\Models\AccessModule;
use Sagartakle\Laracrud\Models\Role;
use Sagartakle\Laracrud\Helpers\CustomHelper;

class ModulesController extends StlcController
{
    public $crud,$setting_crud;

    function __construct() {

        $this->crud = new ObjectHelper;
        $module = (object)[];
        $module->name = "Modules";
        $module->label = "Modules";
        $module->table_name = "modules";
        $module->controller = self::class;
        $module->represent_attr = "label";
        $module->icon = "fa fa-briefcase";
        $module->model = Module::class;

        $module->fields = [
            [
				'name' => 'name',
				'label' => 'Name',
				'field_type' => 'Text',
				'required' => true
			],[
				'name' => 'label',
				'label' => 'label',
				'field_type' => 'Text',
				'required' => true
			],[
				'name' => 'table_name',
				'label' => 'Table Name',
				'field_type' => 'Text',
				'required' => true
			],[
				'name' => 'model',
				'label' => 'Model',
				'field_type' => 'Text',
				'required' => true
			],[
				'name' => 'controller',
				'label' => 'Controller',
				'field_type' => 'Text',
				'required' => true
			],[
				'name' => 'represent_attr',
				'label' => 'Represent Attribute',
				'field_type' => 'Text',
				'required' => true
			],[
				'name' => 'icon',
				'label' => 'Icon',
				'field_type' => 'Text',
				'required' => true
			]
        ];
        // echo "<pre>".json_encode($module->fields,JSON_PRETTY_PRINT);exit;
        $this->crud->setModule($module);
        $this->crud->setRoute(config("stlc.stlc_route_prefix",'developer') . '/'.$module->table_name);

        $this->crud_filed = new ObjectHelper;
        $module = (object)[];
        $module->name = "Fields";
        $module->label = "Fields";
        $module->table_name = "fields";
        $module->controller = "ModulesController";
        $module->represent_attr = "label";
        $module->icon = "fa fa-list";
        $module->model = Field::class;

        $module->fields = [
            [
				'name' => 'name',
				'label' => 'Name',
				'field_type' => 'Text',
				'required' => true
			],[
				'name' => 'label',
				'label' => 'label',
				'field_type' => 'Text',
				'required' => true
			],[
				'name' => 'rank',
				'label' => 'Rank',
				'field_type' => 'Number',
				'required' => true
			],[
				'name' => 'module_id',
				'label' => 'Module',
				'field_type' => 'Select2',
				'required' => true,
                'json_values' => '@'.(Module::class).'|name'
			],[
				'name' => 'field_type_id',
				'label' => 'Field Type',
				'field_type' => 'Select2',
				'required' => true,
                'json_values' => '@'.(FieldType::class).'|name'
			],[
				'name' => 'unique',
				'label' => 'Unique',
				'field_type' => 'Radio',
				'required' => true
			],[
				'name' => 'defaultvalue',
				'label' => 'Default Value',
				'field_type' => 'Text'
			],[
				'name' => 'minlength',
				'label' => 'Min Length',
				'field_type' => 'Number'
			],[
				'name' => 'maxlength',
				'label' => 'Max Length',
				'field_type' => 'Number'
			],[
				'name' => 'required',
				'label' => 'Required',
				'field_type' => 'Radio',
				'required' => true
			],[
				'name' => 'show_index',
				'label' => 'Show Index',
				'field_type' => 'Radio',
				'required' => true
			],[
				'name' => 'json_type',
				'label' => 'Json Type',
				'field_type' => 'Radio',
				'defaultvalue' => 'Module',
				'required' => true,
                'json_values' => ['Module','Json']
			],[
				'name' => 'json_values',
				'label' => 'Json Values',
				'field_type' => 'Text'
			]
        ];
        $this->crud_filed->setModule($module);
        $this->crud_filed->setRoute(config("stlc.stlc_route_prefix",'developer') . '/'.$module->table_name);
        
        if(class_exists(\App\Models\Setting::class)) {
            $this->setting_crud = new ObjectHelper;
            $crud = (object)[];
            $crud->name = "Settings";
            $crud->label = "Settings";
            $crud->table_name = "settings";
            $crud->controller = "ModulesController";
            $crud->represent_attr = "key";
            $crud->icon = "fa fa-cog";
            $crud->model = (\App\Models\Setting::class);
            $setting_keys = \App\Models\Setting::select('key')->get()->pluck('key');
            $setting_keys = collect(config('stlc.setting_keys'))->whereNotIn('key',$setting_keys)->pluck('key');
            
            $crud->fields = [
                [
                    'name' => 'key',
                    'label' => 'Key',
                    'field_type' => 'Select2',
                    'required' => true,
                    'show_index' => true,
                    'json_values' => $setting_keys
                ],[
                    'name' => 'value',
                    'label' => 'Vlaue',
                    'field_type' => 'Text',
                    'required' => true,
                    'show_index' => true
                ]
            ];

            $this->setting_crud->setModule($crud);
            $this->setting_crud->setRoute(config("stlc.stlc_route_prefix",'developer') . '/'.$crud->table_name);
        } else {
            $this->setting_crud = ["\App\Models\Setting not found"];
        }
    }

    public function dashboard(Request $request)
    {
        return view(config('stlc.stlc_modules_folder_name','stlc::').'layouts.app');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if(Auth::user()->isSuperAdmin()) {
            $crud = $this->crud;
            $crud->removeButton('deleted_data');
            if(isset($request->src_ajax) && $request->src_ajax) {
                $modules = Module::custome_all_modules();
            } else {
                $modules = Module::all();
            }
            
            $crud->datatable = true;
            // echo "<pre>".json_encode($crud->fields,JSON_PRETTY_PRINT);exit;

            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'message' => 'created', 'item' => $modules]);
            } else {
                return view(config('stlc.stlc_modules_folder_name','stlc::').'Modules.index', [
                    'crud' => $crud,
                    'modules' => $modules
                ]);
            }
        } else {
            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'failed', 'message' => 'Unauthorized Access']);
            } else {
                abort(403, 'Unauthorized Access');
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select2(Request $request)
    {
        $modules = Module::custome_all_modules();
        
        if(isset($request->searchTerm)) {
            $fetchData = $modules->where('name', 'like', '%'.$request->searchTerm.'%')->get();
        } else {
            $fetchData = $modules->get();
        }

        $data = array();
        foreach ($fetchData as $row) {
            $data[] = array("id"=>$row->name, "text"=>$row->name);
        }
        return response()->json($data);
    }

    public function getModuleData(Request $request)
    {
        $module = Module::where('model',$request->model)->first();
        if(isset($module->name) && in_array($module,['MasterUsers','Employees','PartnerUsers'])) {
            $data = (new $module->model)->get();
        } else {
            $data = (new $module->model)->select(['id',$module->represent_attr])->get();
        }
        return response()->json(['statusCode' => '200','message' => 'success','data' => $data]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(Auth::user()->isSuperAdmin()) {
            if(isset($request->src)) {
                $src = $request->src;
            } else {
                $src = Null;
            }
            
            $crud = $this->crud;

            return view(config('stlc.stlc_modules_folder_name','stlc::').'Modules.create', [
                'crud' => $crud,
                'src' => $src
            ]);
        } else {
            abort(403, 'Unauthorized Access');
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
        if(Auth::user()->isSuperAdmin()) {
            if (is_null($request)) {
                $request = \Request::instance();
            }
            
            $rules = Module::validateRules("Modules", $request);
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
            $item = $this->crud->create($request);
            $this->data['entry'] = $this->crud->entry = $item;

            // add activity log
            // \Activity::log(config('activity_log.context.CREATED'), $this->crud, ['new' => $item]);

            // show a success message
            if(!$request->src_ajax) {
                \Alert::success($this->crud->label." The item has been added successfully.")->flash();
            }

            if(isset($request->go_view) && $request->go_view) {
                return redirect($this->crud->route.'/'.$item->id);
            } else if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'message' => 'created', 'item' => $item]);
            } else if(isset($request->src)) {
                return redirect($request->src);
            } else {
                return redirect($this->crud->route);
            }
        } else {
            abort(403, 'Unauthorized Access');
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
        if(Auth::user()->isSuperAdmin()) {
            if(isset($request->src)) {
                $src = url($request->src);
            } else {
                $src = Null;
            }

            $module = Module::where('id',$id)->first();
            if ((isset($module->id) && !(isset($module->deleted_at) && $module->deleted_at)) || (isset($module->deleted_at) && $module->deleted_at && \Auth::user()->isAdmin())) {

                $crud = $this->crud;
                $crud->datatable = true;
                $crud_filed = $this->crud_filed;
                $crud_filed->datatable = true;
                $crud->row = $module;
            
                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'success', 'message' => 'updated', 'item' => $module]);
                } else {
                    return view(config('stlc.stlc_modules_folder_name','stlc::').'Modules.show', [
                        'crud' => $crud,
                        'crud_filed' => $crud_filed,
                        'module' => $module,
                        'src' => $src,
                        'represent_attr' => $crud->module->represent_attr,
                        'fieldTypes' => $fieldTypes = FieldType::all()
                    ]);
                }
            } else {
                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'failed', 'message' => trans('stlc.data_not_found')]);
                } else {
                    return view('errors.404', [
                        'record_id' => $id,
                        'record_name' => ucfirst("modules"),
                    ]);
                }
            }
        } else {
            if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                return response()->json(['status' => 'failed', 'message' => 'Unauthorized Access']);
            } else {
                abort(403, 'Unauthorized Access');
            }
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
        if(Auth::user()->isSuperAdmin()) {
            if(isset($request->src)) {
                $src = $request->src;
            } else {
                $src = Null;
            }
            
            $module = Module::find($id);
            if(isset($module->id)) {
                
                $crud = $this->crud;
                $crud->row = $module;
            
                return view(config('stlc.stlc_modules_folder_name','stlc::').'Modules.edit', [
                    'crud' => $crud,
                    'module' => $module,
                    'src' => $src
                ]);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("modules"),
                ]);
            }
        } else {
            abort(403, 'Unauthorized Access');
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
        if(Auth::user()->isSuperAdmin()) {
            // old data
            $old_item = Module::find($id);
            if(isset($old_item->id)) {
                if (is_null($request)) {
                    $request = \Request::instance();
                }

                $rules = Module::validateRules("Modules", $request, true);
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
                $item = $this->crud->update($id, $request);
                $this->data['entry'] = $this->crud->entry = $item;

                // add activity log
                // \Activity::log(config('activity_log.context.UPDATED'), $this->crud, ['new' => $item, 'old' => $old_item]);

                // show a success message
                if(!$request->src_ajax) {
                    \Alert::success($this->crud->label." ".trans('stlc.update_success'))->flash();
                }

                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'message' => 'updated', 'item' => $item]);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    return redirect($this->crud->route);
                }
            } else {
                abort(403, trans('stlc.data_not_found'));
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleted_data()
    {
		if(Auth::user()->isSuperAdmin()) {
            $crud = $this->crud;
            $modules = Module::onlyTrashed()->get();
            $crud->onlyButton('restore');
            $crud->labelPlural = trans('stlc.delete')." ".$crud->labelPlural;
            $crud->datatable = true;
            return view(config('stlc.stlc_modules_folder_name','stlc::').'Modules.index', [
                'crud' => $crud,
                'modules' => $modules,
                'btn_hide' => true
            ]);
        } else {
            abort(403, 'Unauthorized Access');
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
        if(Auth::user()->isSuperAdmin()) {
            // old data
            $old_item = Module::find($id);
            if(isset($old_item->id)) {
                $module = Module::find($id)->delete();

                // add activity log
                // \Activity::log(config('activity_log.context.DELETED'), $this->crud, ['old' => $old_item]);
                
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'message' => trans('stlc.delete_confirmation_message')]);
                } else if(isset($request->src)) {
                    Alert::success(trans('stlc.delete_confirmation_message'))->flash();
                    return redirect($request->src);
                } else {
                    Alert::success(trans('stlc.delete_confirmation_message'))->flash();
                    // return redirect()->route(config('aquaspade.base.route_prefix') . 'modules');
                    return (string) $module;
                }
            } else {
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'failed', 'message' => trans('stlc.data_not_found')]);
                } else {
                    abort(403, trans('stlc.data_not_found'));
                }
            }
        } else {
            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'failed', 'message' => 'Unauthorized Access']);
            } else {
                abort(403, 'Unauthorized Access');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        if(Auth::user()->isSuperAdmin()) {
            // old data
            $old_item = Module::onlyTrashed()->find($id);
            if(isset($old_item->id)) {
                $module = Module::onlyTrashed()->find($id)->restore();

                // add activity log
                // \Activity::log(config('activity_log.context.restore'), $this->crud, ['old' => $old_item]);
                
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'message' => 'restore']);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    // return redirect()->route(config('stlc.route_prefix') . 'crud.modules.index');
                    return (string) $module;
                }
            } else {
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'failed', 'message' => trans('stlc.data_not_found')]);
                } else {
                    abort(403, trans('stlc.data_not_found'));
                }
            }
        } else {
            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'failed', 'message' => 'Unauthorized Access']);
            } else {
                abort(403, 'Unauthorized Access');
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
        $listing_cols = Module::getListingColumns('Modules');
        
        if(isset($request->filter)) {
			$values = DB::table('modules')->select($listing_cols)->whereNull('deleted_at')->where($request->filter);
		} else {
			$values = DB::table('modules')->select($listing_cols)->whereNull('deleted_at');
		}
        
        $out = Datatables::of($values)->make();
        $data = $out->getData();
        
        $fields_popup = Field::getFields('Modules');
        
        // array_splice($listing_cols, 2, 0, "index_name");
        
        for($i = 0; $i < count($data->data); $i++) {
            $data->data[$i] = collect($data->data[$i])->values()->all();
            $crud->row = $item = Module::find($data->data[$i][0]);
            // array_splice($data->data[$i], 2, 0, true);
            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];
                $data->data[$i][$j] = \FormBuilder::get_field_value($crud, $col);
                if(isset($data->data[$i][$j]) && $col == $crud->module->represent_attr && !isset($item->deleted_at)) {
                    $data->data[$i][$j] = '<a href="' . url($crud->route .'/'. $item->id) . '">' . $data->data[$i][$j] . '</a>';
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
    
    public function add_field(Request $request)
    {
        if(Auth::user()->isSuperAdmin()) {
            if (is_null($request)) {
                $request = \Request::instance();
            }
            
            $rules = Module::validateRules("Modules", $request);
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
            
            if(isset($request->json_type) && $request->json_type == "Json") {
                if(isset($request->json_values) && is_array($request->json_values)) {
                    $request['json_values'] = json_encode(array_filter($request->json_values));
                } else {
                    $request['json_values'] = '[]';
                }
            } else {
                if(isset($request->json_values) && !empty($request->json_values)) {
                    $request['json_values'] = '@'.$request->json_values;
                } else {
                    $request['json_values'] = "";
                }
            }

            // insert item in the db
            $item = $this->crud_filed->create($request->except(['json_type']));
            $this->data['entry'] = $this->crud_filed->entry = $item;

            // add activity log
            // \Activity::log(config('activity_log.context.CREATED'), $this->crud, ['new' => $item]);

            // show a success message
            if(!$request->src_ajax) {
                \Alert::success($this->crud_filed->label." The item has been added successfully.")->flash();
            }

            if(isset($request->go_view) && $request->go_view) {
                return redirect($this->crud->route);
            } else if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'message' => 'created', 'item' => $item]);
            } else if(isset($request->src)) {
                return redirect($request->src);
            } else {
                if(isset($item->module->id)) {
                    return redirect($this->crud->route.'/'.$item->module->id);
                } else {
                    return redirect($this->crud->route);
                }
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }

    public function edit_field(Request $request, $id)
    {
        if(Auth::user()->isSuperAdmin()) {
            if(isset($request->src)) {
                $src = $request->src;
            } else {
                $src = Null;
            }
            
            $field = Field::find($id);
            if(isset($field->id)) {
                
                $crud = $this->crud_filed;
                $crud->row = $field;
            
                return view(config('stlc.stlc_modules_folder_name','stlc::').'Modules.edit_field', [
                    'crud' => $crud,
                    'field' => $field,
                    'src' => $src
                ]);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("fields"),
                ]);
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_field(Request $request, $id)
    {
        if(Auth::user()->isSuperAdmin()) {
            if(isset($request->src)) {
                $src = url($request->src);
            } else {
                $src = Null;
            }

            $field = Field::find($id);
            if (isset($field->id)) {

                $crud = $this->crud_filed;
                $crud->datatable = true;
                $crud->row = $field;

                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'success', 'message' => 'updated', 'item' => $field]);
                } else {
                    return view(config('stlc.stlc_modules_folder_name','stlc::').'Modules.show_field', [
                        'crud' => $crud,
                        'field' => $field,
                        'src' => $src,
                        'represent_attr' => $crud->module->represent_attr,
                        'fieldTypes' => $fieldTypes = FieldType::all()
                    ]);
                }
            } else {
                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'failed', 'message' => trans('stlc.data_not_found')]);
                } else {
                    return view('errors.404', [
                        'record_id' => $id,
                        'record_name' => ucfirst("fields"),
                    ]);
                }
            }
        } else {
            if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                return response()->json(['status' => 'failed', 'message' => 'Unauthorized Access']);
            } else {
                abort(403, 'Unauthorized Access');
            }
        }
    }
    
    public function update_field(Request $request,$id)
    {
        if(Auth::user()->isSuperAdmin()) {
            // old data
            $old_item = Field::find($id);
            if(isset($old_item->id)) {
                if (is_null($request)) {
                    $request = \Request::instance();
                }

                $rules = Module::validateRules($this->crud_filed->name, $request, true);
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
                if(isset($request->json_type) && $request->json_type == "Json") {
                    if(isset($request->json_values) && is_array($request->json_values)) {
                        $request['json_values'] = json_encode(array_filter($request->json_values));
                    } else {
                        $request['json_values'] = '[]';
                    }
                } else {
                    if(isset($request->json_values) && !empty($request->json_values)) {
                        $request['json_values'] = '@'.$request->json_values;
                    } else {
                        $request['json_values'] = "";
                    }
                }

                // update the row in the db
                $item = $this->crud_filed->update($id, $request->except('json_type'));
                $this->data['entry'] = $this->crud_filed->entry = $item;

                // add activity log
                // \Activity::log(config('activity_log.context.UPDATED'), $this->crud, ['new' => $item, 'old' => $old_item]);

                // show a success message
                if(!$request->src_ajax) {
                    \Alert::success($this->crud_filed->label." ".trans('stlc.update_success'))->flash();
                }

                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'message' => 'updated', 'item' => $item]);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    if(isset($item->module->id)) {
                        return redirect($this->crud->route.'/'.$item->module->id);
                    } else {
                        return redirect($this->crud->route);
                    }
                }
            } else {
                abort(403, trans('stlc.data_not_found'));
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy_field(Request $request, $id)
    {
        if(Auth::user()->isSuperAdmin()) {
            // old data
            $old_item = Field::find($id);
            if(isset($old_item->id)) {
                $field = Field::find($id)->delete();

                // add activity log
                // \Activity::log(config('activity_log.context.DELETED'), $this->crud, ['old' => $old_item]);
                
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'message' => trans('stlc.delete_confirmation_message')]);
                } else if(isset($request->src)) {
                    Alert::success(trans('stlc.delete_confirmation_message'))->flash();
                    return redirect($request->src);
                } else {
                    Alert::success(trans('stlc.delete_confirmation_message'))->flash();
                    // return redirect()->route(config('stlc.route_prefix') . 'modules');
                    return (string) $module;
                }
            } else {
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'failed', 'message' => trans('stlc.data_not_found')]);
                } else {
                    abort(403, trans('stlc.data_not_found'));
                }
            }
        } else {
            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'failed', 'message' => 'Unauthorized Access']);
            } else {
                abort(403, 'Unauthorized Access');
            }
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setting_list()
    {
        if(Auth::user()->isAdmin()) {
            $settings = Setting::all();
            if(isset($this->setting_crud->fields['value'])){
                unset($this->setting_crud->fields['value']);
            }
            $this->setting_crud->create_button = 'modal';
            return view(config('stlc.stlc_modules_folder_name','stlc::').'Modules.settings', [
                'crud' => $this->setting_crud,
                'settings' => $settings
            ]);
        } else {
            abort(403, 'Unauthorized Access');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setting_edit(Request $request, $id)
    {
        if(Auth::user()->isAdmin()) {
            
            $setting = Setting::find($id);
            if(isset($this->setting_crud->fields['key'])){
                unset($this->setting_crud->fields['key']);
            }
            $keys = collect(config('stlc.setting_keys'))->where('key',$setting->key);
            if(isset($setting->id) && $keys->count()) {

                $setting_keys = $keys->first()['type'];
                
                $this->setting_crud->fields['value']->field_type = $setting_keys;
                
                $this->setting_crud->row = $setting; 
                return view(config('stlc.stlc_modules_folder_name','stlc::').'Modules.setting_edit', [
                    'crud' => $this->setting_crud,
                    'setting' => $setting
                ]);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("Settings"),
                ]);
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setting_store(Request $request)
    {
        if(Auth::user()->isAdmin()) {
            if (is_null($request)) {
                $request = \Request::instance();
            }
            $request['value'] = '1';

            $setting_keys = Setting::select('key')->get()->pluck('key');
            $setting_keys = collect(config('stlc.setting_keys'))->whereNotIn('key',$setting_keys)->implode('key',',');
            $rules = [
                'key' => 'required|in:'.$setting_keys
            ];

            $validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'validation_error', 'message' => 'created', 'errors' => $validator->errors()]);
                } else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
			}

            // replace empty values with NULL, so that it will work with MySQL strict mode on
            foreach ($request->input() as $key => $value) {
                if (empty($value) && $value !== '0') {
                    $request->request->set($key, null);
                }
            }

            // insert item in the db
            $item = $this->setting_crud->create($request);
            if (($item instanceof \Illuminate\Http\RedirectResponse) || ($item instanceof \Illuminate\Http\JsonResponse)) {
                return $item;
            }
            
            $this->data['entry'] = $this->setting_crud->entry = $item;
            
            // add activity log
            // \Activity::log(config('App.activity_log.CREATED'), $this->crud, ['new' => $item]);

            // show a success message
            if(!$request->src_ajax) {
                \Alert::success("The item has been added successfully.")->flash();
            }

            if(isset($request->go_view) && $request->go_view) {
                return redirect($this->setting_crud->route.'/'.$item->id);
            } else if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'message' => 'created', 'item' => $item]);
            } else if(isset($request->src)) {
                return redirect($request->src);
            } else {
                return redirect($this->setting_crud->route);
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }

    public function setting_update(Request $request,$id)
    {
        if(Auth::user()->isAdmin()) {
            $old_item = Setting::find($id);
            if(isset($old_item->id)) {
                if (is_null($request)) {
                    $request = \Request::instance();
                }
                if($request->has('key')) {
                    unset($request['key']);
                }

                $rules = [
                    'value' => 'required|max:255'
                ];
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
                $item = $this->setting_crud->update($id, $request);
                $this->data['entry'] = $this->setting_crud->entry = $item;

                // add activity log
                // \Activity::log(config('activity_log.context.UPDATED'), $this->crud, ['new' => $item, 'old' => $old_item]);

                // show a success message
                if(!$request->src_ajax) {
                    \Alert::success($this->setting_crud->label." ".trans('stlc.update_success'))->flash();
                }

                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'message' => 'updated', 'item' => $item]);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    if(isset($item->module->id)) {
                        return redirect($this->setting_crud->route.'/'.$item->module->id);
                    } else {
                        return redirect($this->setting_crud->route);
                    }
                }
            } else {
                abort(403, trans('stlc.data_not_found'));
            }
        } else {
            abort(403, 'Unauthorized Access');
        }
    }

    /**
     * comment on the context.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comment(Request $request, $id)
    {
        $rules = [
            'commentable_type' => 'required',
            'commentable_id' => 'required',
            'comment' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => "", 'error' => $validator]);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if(isset($request->commentable_type) && class_exists($request->commentable_type)) {
            // old data
            $old_item = $request['commentable_type']::find($request->commentable_id);
            
            if(isset($old_item->id)) {
                $comment = $old_item->comment($request->comment);
                $crud_comment = (object)['model'=>(new \App\Models\Comment),'action' => 'Created','description' => 'Comment Created'];
                // add activity log
                \Activity::log('Created', $crud_comment, ['new' => $comment]);
                return response()->json(['status' => 'success', 'message' => 'updated', 'item' => $comment]);
            } else {
                return response()->json(['status' => 'failed', 'message' => trans('stlc.data_not_found'), 'item'=> $old_item]);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'modal class not exist']);
        }
    }

    /**
     * comment_history on the context.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comment_history(Request $request, $id)
    {
        $validator = Validator::make($request->all(), ['commentable_type' => 'required']);
        
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => "validation error", 'error' => $validator]);
        }
        
        if(isset($request->commentable_type) && class_exists($request->commentable_type)) {
            $item = $request['commentable_type']::find($id);
            if(isset($item->id)) {
                // update the row in the db
                $htmlData = \View::make('inc.comment.comment_history',[
                    'commentable_type' => get_class($item),
                    'item' => $item
                ])->render();
                
                return response()->json(['status' => 'success', 'message' => 'get html data', 'htmlData' => $htmlData]);
            } else {
                return response()->json(['status' => 'failed', 'message' => trans('stlc.data_not_found'), 'item'=> $item]);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'modal class not exist']);
        }
    }

    /**
     * save access of role and user perissions.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function module_permissions(Request $request, $id)
	{
        $validator = Validator::make($request->all(),[
            'assessor' => 'required|in:user,role'
        ]);
                
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if($request->assessor == 'user') {
            $assessor = User::find($id);
        } else if($request->assessor == 'role') {
            $assessor = Role::find($id);
        }
		if(\Auth::user()->isAdmin() && isset($assessor->id)) {
            
            $modules_access = Module::access_modules($assessor);
            
			foreach($modules_access as $module) {
                $module_name = $module->name;
                // echo json_encode($request->$module_name);
                $access_arr = ['view','create','edit','deactivate'];
                // 1. Set Module Access
                foreach($access_arr as $access) {
                    if(isset($request->$module_name) && in_array($access, $request->$module_name)) {
                        AccessModule::withTrashed()->updateOrCreate([
                            'assessor_id' => $assessor->id,
                            'assessor_type' => get_class($assessor),
                            'accessible_id' => $module->id,
                            'accessible_type' => get_class($module),
                            'access' => $access
                        ],[
                            'deleted_at' => NULL
                        ]);
                        // echo json_encode($access);
                    } else {
                        // echo 'delete';
                        // echo json_encode($access);

                        AccessModule::where([
                            ['assessor_id', $assessor->id],
                            ['assessor_type', get_class($assessor)],
                            ['accessible_id' , $module->id],
                            ['accessible_type' , get_class($module)],
                            ['access' , $access]
                        ])->delete();
                    }
                }
            }
            \Alert::success("Modify all permissions.")->flash();
            return redirect($request->back_url);
        } else {
            if(!isset($employee->user()->id)) {
                Alert::error("Employee user not exit. please edit employee, give password & generate user")->flash();
                return redirect()->back();
            } else {
                abort(403, 'Unauthorized Access');
            }
        }
    }

    public function crudTable(Request $request)
    {
        $rules['crud'] = 'required|exists:modules,name';
        $rules['prefix'] = 'required|in:'.config('stlc.route_prefix', 'admin').','.config('stlc.partner_route_prefix','partner');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['statusCode' => 400, 'message' => 'Validation Error', 'errors' => $validator->errors()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
        $data = $request->only(['add_btn','table_class','class']);
        $data['crud'] = module::make($request->crud,['route_prefix' => $request->prefix]); 
        if(isset($request->src_ajax) && $request->src_ajax) {
            return response()->json([
                'statusCode' => 200, 'message' => 'succeess',
                'route' => url($data['crud']->route.'/datatable'),
                'html' => view('inc.table',$data)->render()
            ]);
        } else {
            return view('inc.table',$data);
        }
    }
    
    public function customValidator(Request $request)
    {
        $responce = [];
        if(isset($request->rules) && is_array($request->rules)) {
        $validator = \Validator::make($request->all(), $request->rules);
            if ($validator->fails()) {
                $responce = ['statusCode' => 400, 'message' => 'Validation Error', 'errors' => $validator->errors()];
            } else {
                $responce = ['statusCode' => 200, 'message' => 'valid'];
            }
        } else {
            $responce = ['statusCode' => 400, 'message' => 'rules not valid'];
        }
        
        if(isset($request->boolean) && $request->boolean == true) {
            return response()->json(!$validator->fails());
        } else {
            return response()->json($responce);
        }
    }
}
