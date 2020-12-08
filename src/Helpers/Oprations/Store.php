<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

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
            abort(403, trans('stlc.unauthorized_access'));
        }
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function beforeValidationStore(Request $request){
        $request->validate(\Module::validateRules($this->crud->name, $request));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function beforeStore(Request $request){}
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function onStore(Request $request){
        $this->beforeStore($request);
        // insert item in the db
        $this->crud->entry = $item = $this->crud->create($request);
        if (($item instanceof \Illuminate\Http\RedirectResponse) || ($item instanceof \Illuminate\Http\JsonResponse)) {
            return $item;
        }
        return $this->afterStore($request,$item);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function afterStore(Request $request,$item){
        if(!$request->wantsJson()) {
            Alert::success($this->crud->label." ".trans('stlc.insert_success'))->flash();
        }

        if($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $this->crud->label." ".trans('stlc.insert_success'), 'item' => $item],200);
        } else if(isset($request->go_view) && $request->go_view) {
            return redirect($this->crud->route.'/'.$item->id);
        } else  if(isset($request->src)) {
            return redirect($request->src);
        } else {
            return redirect($this->crud->route);
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
            $this->beforeValidationStore($request);
            foreach ($request->input() as $key => $value) {
                if (empty($value) && $value !== '0') {
                    $request->request->set($key, null);
                }
            }
            return $this->onStore($request);
        } else {
            if($request->wantsJson()) {
                return response()->json(['status' => '403', 'message' => trans('stlc.unauthorized_access')],403);
            } else {
                abort(403, trans('stlc.unauthorized_access'));
            }
        }
    }
} 