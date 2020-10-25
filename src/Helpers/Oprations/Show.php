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
            
                if($request->wantsJson()) {
                    return response()->json(['status' => '200', 'message' => 'updated', 'item' => $item],200);
                } else {
                    return view($crud->view_path['show'], [
                        'crud' => $crud,
                        'item' => $item,
                        'src' => $request->src ?? null,
                        'represent_attr' => $crud->module->represent_attr
                    ]);
                }
            } else {
                if($request->wantsJson()) {
                    return response()->json(['status' => '404', 'message' => trans('stlc.data_not_found')],404);
                } else {
                    abort(404, $crud->name);
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