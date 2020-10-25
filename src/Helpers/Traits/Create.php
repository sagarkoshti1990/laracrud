<?php

namespace Sagartakle\Laracrud\Helpers\Traits;

use Sagartakle\Laracrud\Models\FieldType;
use Sagartakle\Laracrud\Models\RelationalDataTable;
Use Exception;
use Sagartakle\Laracrud\Models\Activity;

trait Create
{
    /*
    |--------------------------------------------------------------------------
    |                                   CREATE
    |--------------------------------------------------------------------------
    */

    /**
     * Insert a row in the database.
     *
     * @param  [Request] All input values to be inserted.
     *
     * @return [Eloquent Collection]
     */
    public function create($data,$transaction = true)
    {
        $data = $this->decodeJsonCastedAttributes($data);
        $column_names_ralationaldata = collect($this->fields)->where('type','relationalDataFields')->pluck('name');
        $polymorphic_multiple_fields = collect($this->fields)->whereIn('field_type.name',['Polymorphic_multiple',"Files"]);
        if(\Auth::check()) {
            $data['created_by'] = \Auth::id();
        }
        // \CustomHelper::ajprint($polymorphic_multiple_fields);
        if(isset($transaction) && $transaction == true) {
            \DB::beginTransaction();
        }
        try{
            $item = $this->model->create(collect($data)->only($this->column_names)->toArray());
            if($polymorphic_multiple_fields->count() > 0) {
                foreach($polymorphic_multiple_fields as $pm_field) {
                    $pm_value = $data->{$pm_field->name} ?? $data[$pm_field->name] ?? [];
                    if(isset($pm_value)) {
                        $item->polymorphic_save($pm_field->name,$pm_value);
                    }
                }
            }
            if(isset($column_names_ralationaldata) && count($column_names_ralationaldata) > 0) {
                $update_data = collect($data)->only($column_names_ralationaldata)->toArray();
                $i = 0;
                $ftypes = FieldType::getFTypes();
                foreach($update_data as $key => $r_data) {
                    $r_datas[$i]['context_id'] = $item->id ?? null;;
                    $r_datas[$i]['context_type'] = get_class($this->model);
                    $r_datas[$i]['key'] = $key;
                    $r_datas[$i]['value'] = $r_data;
                    $r_datas[$i]['field_type_id'] = $ftypes[$this->fields['feats']->field_type];
                    $r_datas[$i]['created_at'] = date('Y-m-d H:i:s');
                    $r_datas[$i]['updated_at'] = date('Y-m-d H:i:s');
                    // $r_datas[] = RelationalDataTable::where([['key',$key]])->first();
                    $i++;
                }
                RelationalDataTable::insert($r_datas);
            }
            \Activity::log(config('App.activity_log.CREATED','Created'), $this, ['new' => $item]);
            if(isset($transaction) && $transaction == true) {
                \DB::commit();
            }
            return $item;
        } catch (Exception $ex) {
            if(isset($transaction) && $transaction == true) {
                \DB::rollback();
            }
            if(($data instanceof \Illuminate\Http\Request && $data->wantsJson()) || (isset($data->src_ajax) && $data->src_ajax) || isset($data['src_ajax']) && $data['src_ajax']) {
                return response()->json(['status' => 'exception_error', 'message' => 'error', 'errors' => $ex->getMessage()]);
            } else {
                return redirect()->back()->withErrors($ex->getMessage())->withInput();
            }
        }

        // if there are any relationships available, also sync those
        // $this->syncPivot($item, $data);

        return $item;
    }

    /**
     * Get all fields needed for the ADD NEW ENTRY form.
     *
     * @return [array] The fields with attributes and fake attributes.
     */
    public function getCreateFields()
    {
        return $this->create_fields;
    }

    /**
     * Get all fields with relation set (model key set on field).
     *
     * @param [string: create/update/both]
     *
     * @return [array] The fields with model key set.
     */
    public function getRelationFields($form = 'create')
    {
        if ($form == 'create') {
            $fields = $this->create_fields;
        } else {
            $fields = $this->update_fields;
        }

        $relationFields = [];

        foreach ($fields as $field) {
            if (isset($field['model'])) {
                array_push($relationFields, $field);
            }

            if (isset($field['subfields']) &&
                is_array($field['subfields']) &&
                count($field['subfields'])) {
                foreach ($field['subfields'] as $subfield) {
                    array_push($relationFields, $subfield);
                }
            }
        }

        return $relationFields;
    }

    /**
     * Get all fields with n-n relation set (pivot table is true).
     *
     * @param [string: create/update/both]
     *
     * @return [array] The fields with n-n relationships.
     */
    public function getRelationFieldsWithPivot($form = 'create')
    {
        $all_relation_fields = $this->getRelationFields($form);

        return array_where($all_relation_fields, function ($value, $key) {
            return isset($value['pivot']) && $value['pivot'];
        });
    }

    public function syncPivot($model, $data, $form = 'create')
    {
        $fields_with_relationships = $this->getRelationFields($form);

        foreach ($fields_with_relationships as $key => $field) {
            if (isset($field['pivot']) && $field['pivot']) {
                $values = isset($data[$field['name']]) ? $data[$field['name']] : [];
                $model->{$field['name']}()->sync($values);

                if (isset($field['pivotFields'])) {
                    foreach ($field['pivotFields'] as $pivotField) {
                        foreach ($data[$pivotField] as $pivot_id => $field) {
                            $model->{$field['name']}()->updateExistingPivot($pivot_id, [$pivotField => $field]);
                        }
                    }
                }
            }

            if (isset($field['morph']) && $field['morph']) {
                $values = isset($data[$field['name']]) ? $data[$field['name']] : [];
                if ($model->{$field['name']}) {
                    $model->{$field['name']}()->update($values);
                } else {
                    $model->{$field['name']}()->create($values);
                }
            }
        }
    }
}
