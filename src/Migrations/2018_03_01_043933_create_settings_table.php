<?php

use Sagartakle\Laracrud\Models\Field;
use Sagartakle\Laracrud\Models\FieldType;
use Sagartakle\Laracrud\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Module::generate('Settings', 'settings', 'value', 'fa-cog', [
            [
                'name' => 'key',
                'label' => 'Key',
                'field_type' => 'Text',
                'unique' => true,
                'required' => true,
                'show_index' => true
            ],[
                'name' => 'value',
                'label' => 'Value',
                'field_type' => 'Text',
                'show_index' => true
            ],[
                'name' => 'context_id',
                'label' => 'Context id',
                'field_type' => 'Hidden',
                'required' => true,
            ],[
                'name' => 'context_type',
                'label' => 'Context type',
                'field_type' => 'Hidden',
                'required' => true,
            ]
        ]);
        
        /*
        Module::generate('Settings' 'settings', 'name', 'fa-cog', [
            [
                'name' => 'name',
                'label' => 'Name',
                'field_type' => 'Name',
                'unique' => false,
                'defaultvalue' => 'John Doe',
                'minlength' => 5,
                'maxlength' => 100,
                'required' => true,
                'nullable_required' => false,
                'show_index' => true,
                'json_values' => ['Employee', 'Client']
            ]
        ]);

        field type [
            Address,
			Checkbox,
			CKEditor,
			Currency,
			Date,
			Date_picker,
			Date_range,
			Datetime,
			Datetime_picker,
			Email,
			File,
			Files,
			Hidden,
			Image,
			Json,
			Month,
			Multiselect,
			Number,
			Password,
			Phone,
			Radio,
			Select,
			Select2,
			Select2_multiple,
			Text,
			Textarea,

        ]

        name: Database column name. lowercase, words concatenated by underscore (_)
        label: Label of Column e.g. Name, Cost, Is Public
        field_type: It defines type of Column in more General way.
        unique: Whether the column has unique values. Value in true / false
        defaultvalue: Default value for column.
        minlength: Minimum Length of value in integer.
        maxlength: Maximum Length of value in integer.
        required: Is this mandatory field in Add / Edit forms. Value in true / false
        show_index: Is allowed to show in index page datatable.
        json_values: These are values for MultiSelect, TagInput and Radio Columns. Either connecting @tables or to list []
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
