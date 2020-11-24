<?php

namespace Sagartakle\Laracrud\Helpers\Traits;

use Sagartakle\Laracrud\Models\Module;

trait Polymorphic
{
    /*
    |--------------------------------------------------------------------------
    |                                   DELETE
    |--------------------------------------------------------------------------
    */

    /**
     * Delete a row from the database.
     *
     * @param  [int] The id of the item to be deleted.
     * @param int $id
     *
     * @return [bool] Deletion confirmation.
     *\CustomHelper::ajprint($data,false);
     * TODO: should this delete items with relations to it too?
     */
    public function polymorphic_save($attr,$value)
    {
        $module = config('stlc.module_model')::where('table_name',$this->getTable())->first();
        $field = $module->fields->firstWhere('name',$attr);
        $polymorphic_module = $field->getJsonModule();
        if(isset($polymorphic_module->id)) {
            $ralasion_field = $polymorphic_module->fields->firstWhere('name',$polymorphic_module->represent_attr);
            $ralasion_module = $ralasion_field->getJsonModule();
            if(isset($ralasion_module->id)) {
                $polymorphic_field = $polymorphic_module->fields->firstWhere('field_type.name','Polymorphic_select');
                $ralasion = $this->morphToMany($ralasion_module->model, $polymorphic_field->name,$polymorphic_module->table_name,null,$polymorphic_module->represent_attr)->withTimestamps()->where('attribute',$attr);
                foreach($value as $key => $val_id) {
                    $value[$val_id] = ['attribute' => $attr];
                }
                $ralasion->sync($value);
            }
        }
        return $this;
    }
}
