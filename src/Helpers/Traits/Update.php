<?php

namespace Sagartakle\Laracrud\Helpers\Traits;

use App\Models\FieldType;
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
    public function update($id, $data)
    {
        $data = $this->decodeJsonCastedAttributes($data);
        $old_item = $this->model->findOrFail($id);
        $item = $this->model->findOrFail($id);
        $column_names_ralationaldata = collect($this->fields)->where('type','relationalDataFields')->pluck('name');
        $polymorphic_multiple_fields = collect($this->fields)->where('field_type.name','Polymorphic_multiple');
        // $this->syncPivot($item, $data, 'update');
        // echo "<pre>";
        // echo json_encode($data, JSON_PRETTY_PRINT);
        // echo "<br><br><br><br>";
        // echo json_encode(collect($data)->only($this->column_names)->toArray(), JSON_PRETTY_PRINT);
        // echo "</pre>";
        // return ;
        $item = \DB::transaction(function() use ($data,$old_item,$item,$column_names_ralationaldata,$polymorphic_multiple_fields) {
            try{
                $updated = $item->update(collect($data)->only($this->column_names)->toArray());
                if($polymorphic_multiple_fields->count() > 0) {
                    foreach($polymorphic_multiple_fields as $pm_field) {
                        if(!isset($data->xeditable) || (isset($data->xeditable) && $data->xeditable != "Yes")) {
                            $item->polymorphic_save($pm_field->name,$data->{$pm_field->name});
                        }
                    }
                }
                if(isset($column_names_ralationaldata) && count($column_names_ralationaldata) > 0) {
                    $update_data = collect($data)->only($column_names_ralationaldata)->toArray();
                    $ftypes = FieldType::getFTypes();
                    foreach($update_data as $key => $value) {
                        $r_datas['field_type_id'] = $ftypes[$this->fields['feats']->field_type];
                        RelationalDataTable::updateOrCreate(
                            ['context_id' => $item->id,'context_type' => get_class($this->model),'key' => $key],
                            ['value' => $value]
                        );
                    }
                }
                Activity::log(config('App.activity_log.UPDATED'), $this, ['new' => $item, 'old' => $old_item]);
                return $item;
            } catch (\Exception $ex) {
                \DB::rollback();
                if(isset($data->src_ajax) && $data->src_ajax) {
                    return response()->json(['status' => 'exception_error', 'message' => 'created', 'errors' => $ex->getMessage()]);
                } else {
                    return redirect()->back()->withErrors($ex->getMessage())->withInput();
                }
            }
        });

        return $item;
    }

    /**
     * Get all fields needed for the EDIT ENTRY form.
     *
     * @param  [integer] The id of the entry that is being edited.
     * @param int $id
     *
     * @return [array] The fields with attributes, fake attributes and values.
     */
    public function getUpdateFields($id)
    {
        $fields = $this->update_fields;
        $entry = $this->getEntry($id);
        
        foreach ($fields as $k => $field) {
            // set the value
            if (! isset($fields[$k]['value'])) {
                if (isset($field['subfields'])) {
                    $fields[$k]['value'] = [];
                    foreach ($field['subfields'] as $key => $subfield) {
                        $fields[$k]['value'][] = $entry->{$subfield['name']};
                    }
                } else {
                    if(isset($field['name'])) {
                        $fields[$k]['value'] = $entry->{$field['name']};
                    } else {
                        $fields[$k]['value'] = $entry->{$field['name']};
                    }
                }
            }
        }

        // always have a hidden input for the entry id
        if (! array_key_exists('id', $fields)) {
            $fields['id'] = [
                'name'  => $entry->getKeyName(),
                'value' => $entry->getKey(),
                'type'  => 'hidden',
            ];
        }

        return $fields;
    }
}
