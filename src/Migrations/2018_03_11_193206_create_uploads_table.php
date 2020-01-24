<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\FieldType;
use Sagartakle\Laracrud\Models\Field;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Module::generate('Uploads', 'uploads', 'name', 'fa-cloud-upload', [
            [
				'name' => 'name',
				'label' => 'Name',
				'field_type' => 'Text',
				'required' => true,
				'show_index' => true
			],[
				'name' => 'path',
				'label' => 'Path',
				'field_type' => 'Text',
				'show_index' => true
			],[
				'name' => 'extension',
				'label' => 'Extension',
				'field_type' => 'Text',
				'show_index' => true
			],[
				'name' => 'caption',
				'label' => 'Caption',
				'field_type' => 'Text',
				'show_index' => true
			],[
				'name' => 'user_id',
				'label' => 'Owner',
				'field_type' => 'Select2',
				'defaultvalue' => '1',
				'show_index' => true,
				'json_values' => '@users'
			],[
				'name' => 'hash',
				'label' => 'Hash',
				'field_type' => 'Text',
				'show_index' => true
			],[
				'name' => 'public',
				'label' => 'Is Public',
				'field_type' => 'Checkbox',
				'show_index' => true
			]
        ]);
        
        /*
        Module::generate('Uploads' 'uploads', 'name', 'fa-cloud-upload', [
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
		Schema::dropIfExists('uploads');
    }
}
