<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Illuminate\Http\Request;

trait Restore
{
    public function beforeRestore(Request $request,$old_item){}
    public function onRestore(Request $request,$old_item){
        $id = $old_item->id;
        $item = $this->crud->model->onlyTrashed()->find($id)->restore();
        return $this->afterRestore($request,$item);
    }
    public function afterRestore(Request $request,$item){
        if($request->wantsJson()) {
            return response()->json([
                'item' => $item,
                'status' => '200',
                'message' => $this->crud->label." ".trans('stlc.restore_success')
            ],200);
        } else if(isset($request->src)) {
            return redirect($request->src);
        } else {
            return (string) $item;
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
        if($this->crud->hasAccess('restore')) {
            $old_item = $this->crud->model->onlyTrashed()->find($id);
            if(isset($old_item->id)) {
                $this->beforeRestore($request,$old_item);
                return $this->onRestore($request,$old_item);
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