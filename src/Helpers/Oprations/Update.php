<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Sagartakle\Laracrud\Models\Module;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

trait Update
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if($this->crud->hasAccess('edit')) {
            $crud = $this->crud;
            $item = $crud->model->find($id);
            if(isset($item->id)) {
                $crud->row = $item;
                return view($crud->view_path['edit'], [
                    'crud' => $crud,
                    'item' => $item,
                    'src' => $request->src ?? null
                ]);
            } else {
                abort(404, $crud->name);
            }
        } else {
            abort(403, trans('stlc.unauthorized_access'));
        }
    }

    public function beforeValidationUpdate(Request $request,$item){}
    public function beforeUpdate(Request $request,$item){}
    public function afterUpdate(Request $request,$item){
        if(!$request->wantsJson()) {
            Alert::success($this->crud->label." ".trans('stlc.update_success'))->flash();
        }
        if($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $this->crud->label." ".trans('stlc.update_success'), 'item' => $item],200);
        } else if(isset($request->src)) {
            return redirect($request->src);
        } else {
            return redirect($this->crud->route);
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
            $crud = $this->crud;
            // old data
            $old_item = $crud->model->find($id);
            if(isset($old_item->id)) {
                $this->beforeValidationUpdate($request, $old_item);
                $request->validate(config('stlc.module_model')::validateRules($crud->name, $request,true));
                
                // replace empty values with NULL, so that it will work with MySQL strict mode on
                foreach ($request->input() as $key => $value) {
                    if (empty($value) && $value !== '0') {
                        $request->request->set($key, null);
                    }
                }
                $this->beforeUpdate($request,$old_item);
                // update the row in the db
                $this->data['entry'] = $this->crud->entry = $item = $this->crud->update($id, $request);
                if (($item instanceof \Illuminate\Http\RedirectResponse) || ($item instanceof \Illuminate\Http\JsonResponse)) {
                    return $item;
                }
                return $this->afterUpdate($request,$item);
            } else {
                if($request->wantsJson()) {
                    return response()->json(['status' => '404', 'message' => trans('stlc.data_not_found')],404);
                } else {
                    abort(404, trans('stlc.data_not_found'));
                }
            }
        } else {
            if($request->wantsJson()) {
                return response()->json(['status' => '403', 'message' => trans('stlc.unauthorized_access')],403);
            } else {
                abort(403, trans('stlc.unauthorized_access'));
            }
        }
    }
}