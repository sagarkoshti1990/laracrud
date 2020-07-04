<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Sagartakle\Laracrud\Models\Module;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

trait Store
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($this->crud->hasAccess('create')) {
            $crud = $this->crud;
            if(isset($request->copy)) {
                $item = $crud->model->find($request->copy);
                if(isset($item->id)) {
                    $crud->row = $item;
                }
            }
            return view($crud->view_path['create'], [
                'crud' => $crud,
                'src' => $request->src ?? null
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
            $request->validate(Module::validateRules($this->crud->name, $request));

            // replace empty values with NULL, so that it will work with MySQL strict mode on
            foreach ($request->input() as $key => $value) {
                if (empty($value) && $value !== '0') {
                    $request->request->set($key, null);
                }
            }

            // insert item in the db
            $this->data['entry'] = $this->crud->entry = $item = $this->crud->create($request);
            if (($item instanceof \Illuminate\Http\RedirectResponse) || ($item instanceof \Illuminate\Http\JsonResponse)) {
                return $item;
            }

            // show a success message
            if(!$request->src_ajax) {
                Alert::success($this->crud->label." ".trans('crud.insert_success'))->flash();
            }

            if(isset($request->go_view) && $request->go_view) {
                return redirect($this->crud->route.'/'.$item->id);
            } else if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'message' => $this->crud->label." ".trans('crud.insert_success'), 'item' => $item]);
            } else if(isset($request->src)) {
                return redirect($request->src);
            } else {
                return redirect($this->crud->route);
            }
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }
} 