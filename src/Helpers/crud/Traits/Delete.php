<?php

namespace Sagartakle\Laracrud\Helpers\crud\Traits;

trait Delete
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
     *
     * TODO: should this delete items with relations to it too?
     */
    public function delete($id)
    {
        $old_item = $this->model->findOrFail($id);
        $item = $this->model->find($id)->delete();
        \Activity::log(config('App.activity_log.DELETED'), $this, ['old' => $old_item]);
        return $item;
    }
}
