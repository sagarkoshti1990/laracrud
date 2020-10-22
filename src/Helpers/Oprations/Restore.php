<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

trait Restore
{
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
            $old_item = $crud->model->find($id);
            if(isset($old_item->id)) {
                $item = $crud->model->onlyTrashed()->find($id)->restore();
                
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'message' => 'restore']);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    return (string) $item;
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
                return response()->json(['status' => 'failed', 'message' => trans('stlc.unauthorized_access')]);
            } else {
                abort(403, trans('stlc.unauthorized_access'));
            }
        }
    }

}