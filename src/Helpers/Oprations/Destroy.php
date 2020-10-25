<?php

namespace Sagartakle\Laracrud\Helpers\Oprations;

use Illuminate\Http\Request;

trait Destroy
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($this->crud->hasAccess('delete')) {
            $crud = $this->crud;
            // old data
            $old_item = $crud->model->find($id);
            if(isset($old_item->id)) {
                $item = $crud->delete($id);

                if($request->wantsJson()) {
                    return response()->json(['status' => '200', 'message' => $this->crud->label." ".trans('stlc.delete_success')],200);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    return (string) $item;
                }
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function permanently_delete(Request $request, $id)
    {
        if($this->crud->hasAccess('permanently_delete')) {
            $crud = $this->crud;
            // old data
            $old_item = $crud->model->onlyTrashed()->find($id);
            if(isset($old_item->id)) {
                $dependancy = $crud->module->delete_dependency($id);
                if(count($dependancy) == 0) {
                    // $item = $crud->model->onlyTrashed()->forceDelete($id);
                
                    if($request->wantsJson()) {
                        return response()->json(['status' => '200', 'message' => $this->crud->label." ".trans('stlc.delete_success'),'data'=>count($dependancy)],200);
                    } else if(isset($request->src)) {
                        return redirect($request->src);
                    } else {
                        return (string) $item;
                    }
                } else {
                    if($request->wantsJson()) {
                        return response()->json(['status' => '403', 'message' => $this->crud->label." ".trans('stlc.delete_dependency_success'),'data' => $dependancy],403);
                    } else {
                        abort(403, $this->crud->label." ".trans('stlc.delete_dependency_success'));
                    }
                }
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