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
            abort(403, trans('crud.unauthorized_access'));
        }
    }

    public function beforeUpdate(Request $request,$item){}
    public function afterUpdate(Request $request,$item){}
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
                $request->validate(Module::validateRules($crud->name, $request,true));
                
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
                $this->afterUpdate($request,$item);
                // show a success message
                if(!$request->src_ajax) {
                    \Alert::success($this->crud->label." ".trans('crud.update_success'))->flash();
                }
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'message' => $this->crud->label." ".trans('crud.update_success'), 'item' => $item]);
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
}