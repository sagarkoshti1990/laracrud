<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

trait Update
{
    public function beforeEdit(Request $request,$item){}
    public function onEdit(Request $request,$item){
        $this->crud->row = $item;
        return view($this->crud->view_path['edit'], [
            'crud' => $this->crud,
            'item' => $item,
            'src' => $request->src ?? null
        ]);
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
            $this->crud = $this->crud;
            $item = $this->crud->model->find($id);
            if(isset($item->id)) {
                $this->beforeEdit($request,$item);
                return $this->onEdit($request,$item);
            } else {
                abort(404, $this->crud->name);
            }
        } else {
            abort(403, trans('stlc.unauthorized_access'));
        }
    }

    public function beforeValidationUpdate(Request $request,$item){
        $request->validate(\Module::validateRules($this->crud->name, $request,true));
    }
    public function beforeUpdate(Request $request,$item){}
    public function onUpdate(Request $request,$old_item){
        $this->beforeUpdate($request,$old_item);
        // update the row in the db
        $this->data['entry'] = $this->crud->entry = $item = $this->crud->update($old_item->id, $request);
        if (($item instanceof \Illuminate\Http\RedirectResponse) || ($item instanceof \Illuminate\Http\JsonResponse)) {
            return $item;
        }
        return $this->afterUpdate($request,$item);
    }
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
            $old_item = $this->crud->model->find($id);
            if(isset($old_item->id)) {
                $this->beforeValidationUpdate($request, $old_item);
                // replace empty values with NULL, so that it will work with MySQL strict mode on
                foreach ($request->input() as $key => $value) {
                    if (empty($value) && $value !== '0') {
                        $request->request->set($key, null);
                    }
                }
                return $this->onUpdate($request,$old_item);
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