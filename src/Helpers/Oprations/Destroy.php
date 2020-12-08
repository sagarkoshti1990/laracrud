<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Illuminate\Http\Request;

trait Destroy
{
    public function beforeDestroy(Request $request,$old_item){}
    public function onDestroy(Request $request,$old_item){
        $id = $old_item->id;
        $item = $this->crud->delete($id);
        return $this->afterDestroy($request,$id);
    }
    public function afterDestroy(Request $request,$id){
        if($request->wantsJson()) {
            return response()->json(['status' => '200', 'message' => $this->crud->label." ".trans('stlc.delete_success')],200);
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
    public function destroy(Request $request, $id)
    {
        if($this->crud->hasAccess('delete')) {
            $old_item = $this->crud->model->find($id);
            if(isset($old_item->id)) {
                $this->beforeDestroy($request,$old_item);
                return $this->onDestroy($request,$old_item);
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

    /**
     * PermanentlyDelete from storage.
     *
     */
    public function beforePermanentlyDelete(Request $request,$old_item){}
    public function onPermanentlyDelete(Request $request,$old_item){
        $id = $old_item->id;
        $dependancy = $this->crud->module->delete_dependency($id);
        if(count($dependancy) == 0) {
            return $this->afterPermanentlyDelete($request,$old_item,$dependancy);
        } else {
            if($request->wantsJson()) {
                return response()->json(['status' => '403', 'message' => $this->crud->label." ".trans('stlc.delete_dependency_success'),'data' => $dependancy],403);
            } else {
                abort(403, $this->crud->label." ".trans('stlc.delete_dependency_success'));
            }
        }
    }
    public function afterPermanentlyDelete(Request $request,$item,$dependancy){
        if($request->wantsJson()) {
            return response()->json([
                'status' => '200',
                'message' => $this->crud->label." ".trans('stlc.delete_success'),
                'data'=>count($dependancy)
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
    public function permanently_delete(Request $request, $id)
    {
        if($this->crud->hasAccess('permanently_delete')) {
            $old_item = $this->crud->model->onlyTrashed()->find($id);
            if(isset($old_item->id)) {
                $this->beforePermanentlyDelete($request,$old_item);
                return $this->onPermanentlyDelete($request,$old_item);
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