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
use Sagartakle\Laracrud\Helpers\FormBuilder;

class StlcController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		if($this->crud->hasAccess('view')) {
            
            $crud = $this->crud;
            $item = $this->crud->model->all();

            return view('crud.index', [
                'crud' => $crud,
                'item' => $item
            ]);
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($this->crud->hasAccess('create')) {
            if(isset($request->src)) {
                $src = $request->src;
            } else {
                $src = Null;
            }
            
            $crud = $this->crud;

            return view('crud.form', [
                'crud' => $crud,
                'src' => $src
            ]);
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
        if($this->crud->hasAccess('create')) {
            if (is_null($request)) {
                $request = \Request::instance();
            }
            
            $rules = Module::validateRules($this->crud->name, $request);
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'validation_error', 'massage' => 'Validation Error', 'errors' => $validator->errors()]);
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
            $item = $this->crud->create($request);
            if (($item instanceof \Illuminate\Http\RedirectResponse) || ($item instanceof \Illuminate\Http\JsonResponse)) {
                return $item;
            }
            
            $this->data['entry'] = $this->crud->entry = $item;
            
            // add activity log
            // \Activity::log(config('App.activity_log.CREATED'), $this->crud, ['new' => $item]);

            // show a success message
            if(!$request->src_ajax) {
                \Alert::success($this->crud->label." ".trans('crud.insert_success'))->flash();
            }

            if(isset($request->go_view) && $request->go_view) {
                return redirect($this->crud->route.'/'.$item->id);
            } else if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'massage' => $this->crud->label." ".trans('crud.insert_success'), 'item' => $item]);
            } else if(isset($request->src)) {
                return redirect($request->src);
            } else {
                return redirect($this->crud->route);
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
        if($this->crud->hasAccess('view')) {
            if(isset($request->src)) {
                $src = url($request->src);
            } else {
                $src = Null;
            }

            $item = $this->crud->model->find($id);
            if(isset($item->id)) {
                
                $crud = $this->crud;
                $crud->datatable = true;
                $crud->row = $item;
            
                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'success', 'massage' => 'updated', 'item' => $item]);
                } else {
                    return view('crud.show', [
                        'crud' => $crud,
                        'item' => $item,
                        'src' => $src,
                        'represent_attr' => $crud->module->represent_attr
                    ]);
                }
            } else {
                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'failed', 'massage' => trans('crud.data_not_found')]);
                } else {
                    return view('errors.404', [
                        'record_id' => $id,
                        'record_name' => ucfirst("employees"),
                    ]);
                }
            }
        } else {
            if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                return response()->json(['status' => 'failed', 'massage' => trans('crud.unauthorized_access')]);
            } else {
                abort(403, trans('crud.unauthorized_access'));
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
        if($this->crud->hasAccess('edit')) {
            if(isset($request->src)) {
                $src = $request->src;
            } else {
                $src = Null;
            }
            
            $item = $this->crud->model->find($id);
            if(isset($item->id)) {
                
                $crud = $this->crud;
                $crud->row = $item;
            
                return view('crud.form', [
                    'crud' => $crud,
                    'item' => $item,
                    'src' => $src
                ]);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("employees"),
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
        if($this->crud->hasAccess('edit')) {
            // old data
            $old_item = $this->crud->model->find($id);
            if(isset($old_item->id)) {
                if (is_null($request)) {
                    $request = \Request::instance();
                }

                $rules = Module::validateRules($this->crud->name, $request, true);
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
                if (($item instanceof \Illuminate\Http\RedirectResponse) || ($item instanceof \Illuminate\Http\JsonResponse)) {
                    return $item;
                }
                $this->data['entry'] = $this->crud->entry = $item;

                // add activity log
                // \Activity::log(config('App.activity_log.UPDATED'), $this->crud, ['new' => $item, 'old' => $old_item]);

                // show a success message
                if(!$request->src_ajax) {
                    \Alert::success($this->crud->label." ".trans('crud.update_success'))->flash();
                }

                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'massage' => $this->crud->label." ".trans('crud.update_success'), 'item' => $item]);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    return redirect($this->crud->route);
                }
            } else {
                abort(403, trans('crud.data_not_found'));
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
        if($this->crud->hasAccess('delete')) {
            // old data
            $old_item = $this->crud->model->find($id);
            if(isset($old_item->id)) {
                $item = $this->crud->delete($id);

                // add activity log
                // \Activity::log(config('App.activity_log.DELETED'), $this->crud, ['old' => $old_item]);
                
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'massage' => 'deleted']);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    // return redirect()->route(config('stlc.route_prefix') . 'crud.employees.index');
                    return (string) $item;
                }
            } else {
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'failed', 'massage' => trans('crud.data_not_found')]);
                } else {
                    abort(403, trans('crud.data_not_found'));
                }
            }
        } else {
            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'failed', 'massage' => trans('crud.unauthorized_access')]);
            } else {
                abort(403, trans('crud.unauthorized_access'));
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
        if($this->crud->hasAccess('deactivate')) {
            // old data
            $old_item = $this->crud->model->onlyTrashed()->find($id);
            if(isset($old_item->id)) {
                $item = $this->crud->model->onlyTrashed()->find($id)->restore();

                // add activity log
                // \Activity::log(config('App.activity_log.restore'), $this->crud, ['old' => $old_item]);
                
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'massage' => 'restore']);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    // return redirect()->route(config('stlc.route_prefix') . 'crud.employees.index');
                    return (string) $item;
                }
            } else {
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'failed', 'massage' => trans('crud.data_not_found')]);
                } else {
                    abort(403, trans('crud.data_not_found'));
                }
            }
        } else {
            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'failed', 'massage' => trans('crud.unauthorized_access')]);
            } else {
                abort(403, trans('crud.unauthorized_access'));
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
        $listing_cols = Module::getListingColumns($this->crud->name);
        
        if(isset($request->filter)) {
			$values = DB::table($this->crud->table_name)->select($listing_cols)->whereNull('deleted_at')->where($request->filter);
		} else {
			$values = DB::table($this->crud->table_name)->select($listing_cols)->whereNull('deleted_at');
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
                        $data->data[$i][$j] = FormBuilder::get_field_value($crud, $col, $item->$col);
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
