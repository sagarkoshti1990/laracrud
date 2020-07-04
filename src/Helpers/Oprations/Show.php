<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Illuminate\Http\Request;

trait Show
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if($this->crud->hasAccess('view')) {
            $crud = $this->crud;
            $item = $crud->model->find($id);
            if(isset($item->id)) {
                $crud->datatable = true;
                $crud->row = $item;
            
                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'success', 'message' => 'updated', 'item' => $item]);
                } else {
                    return view($crud->view_path['show'], [
                        'crud' => $crud,
                        'item' => $item,
                        'src' => $request->src ?? null,
                        'represent_attr' => $crud->module->represent_attr
                    ]);
                }
            } else {
                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'failed', 'message' => trans('crud.data_not_found')]);
                } else {
                    abort(404, $crud->name);
                }
            }
        } else {
            if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                return response()->json(['status' => 'failed', 'message' => trans('crud.unauthorized_access')]);
            } else {
                abort(403, trans('crud.unauthorized_access'));
            }
        }
    }
}