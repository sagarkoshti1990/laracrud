<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Illuminate\Http\Request;

trait Show
{
    public function beforeShow(Request $request,$item){}
    public function onShow(Request $request,$item){
        $this->crud->row = $item;
        if($request->wantsJson()) {
            return response()->json(['status' => '200', 'message' => 'updated', 'item' => $item],200);
        } else {
            return view($this->crud->view_path['show'], [
                'crud' => $this->crud,
                'item' => $item,
                'src' => $request->src ?? null,
                'represent_attr' => $this->crud->module->represent_attr
            ]);
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
            $item = $this->crud->model->find($id);
            if(isset($item->id)) {
                $this->beforeShow($request,$item);
                return $this->onShow($request,$item);
            } else {
                if($request->wantsJson()) {
                    return response()->json(['status' => '404', 'message' => trans('stlc.data_not_found')],404);
                } else {
                    abort(404, $this->crud->name);
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