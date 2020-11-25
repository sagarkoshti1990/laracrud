<?php

namespace Sagartakle\Laracrud\Helpers\Traits;

use Sagartakle\Laracrud\Models\FieldType;
use App\Models\RelationalDataTable;
use Sagartakle\Laracrud\Models\Activity;
use Sagartakle\Laracrud\Models\Module;

trait Update
{
    /*
    |--------------------------------------------------------------------------
    |                                   UPDATE
    |--------------------------------------------------------------------------
    */

    /**
     * Update a row in the database.
     *
     * @param  [Int] The entity's id
     * @param  [Request] All inputs to be updated.
     *
     * @return [Eloquent Collection]
     */
    public function update($id, $data,$transaction = true)
    {
        $data = $this->decodeJsonCastedAttributes($data);
        $old_item = $this->model->find($id);
        $item = $this->model->find($id);
        $column_names_ralationaldata = collect($this->fields)->where('type','relationalDataFields')->pluck('name');
        $polymorphic_multiple_fields = collect($this->fields)->whereIn('field_type.name',['Polymorphic_multiple',"Files"]);
        if(isset($transaction) && $transaction == true) {
            \DB::beginTransaction();
        }
        try{
            $updated = $item->update(collect($data)->only($this->column_names)->toArray());
            if($polymorphic_multiple_fields->count() > 0) {
                foreach($polymorphic_multiple_fields as $pm_field) {
                    if(!isset($data->xeditable) || (isset($data->xeditable) && $data->xeditable != "Yes")) {
                        $pm_value = $data->{$pm_field->name} ?? $data[$pm_field->name] ?? [];
                        if(isset($pm_value) && is_array($pm_value)) {
                            $item->polymorphic_save($pm_field->name,$pm_value);
                        }
                    }
                }
            }
            if(isset($column_names_ralationaldata) && count($column_names_ralationaldata) > 0) {
                $update_data = collect($data)->only($column_names_ralationaldata)->toArray();
                $ftypes = config('stlc.field_type_model')::getFTypes();
                foreach($update_data as $key => $value) {
                    $r_datas['field_type_id'] = $ftypes[$this->fields['feats']->field_type];
                    RelationalDataTable::updateOrCreate(
                        ['context_id' => $item->id,'context_type' => get_class($this->model),'key' => $key],
                        ['value' => $value]
                    );
                }
            }
            config('stlc.activity_model')::log(config('App.activity_log.UPDATED','Updated'), $this, ['new' => $item, 'old' => $old_item]);
            if(isset($transaction) && $transaction == true) {
                \DB::commit();
            }
            return $item;
        } catch (\Exception $ex) {
            if(isset($transaction) && $transaction == true) {
                \DB::rollback();
            }
            if(($data instanceof \Illuminate\Http\Request && $data->wantsJson()) || isset($data->src_ajax) && $data->src_ajax || isset($data['src_ajax']) && $data['src_ajax']) {
                return response()->json(['status' => 'exception_error', 'message' => 'error', 'errors' => $ex->getMessage()]);
            } else {
                return redirect()->back()->withErrors($ex->getMessage())->withInput();
            }
        }

        return $item;
    }

}
