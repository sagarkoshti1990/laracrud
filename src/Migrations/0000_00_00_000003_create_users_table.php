<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\FieldType;
use Sagartakle\Laracrud\Models\Field;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Module::generate('Users', 'users', 'last_name', 'fa-group', [
            [
				'name' => 'title',
				'label' => 'Title',
				'field_type' => 'Text',
			],[
				'name' => 'first_name',
				'label' => 'First name',
				'field_type' => 'Text',
				'required' => true
			],[
				'name' => 'last_name',
				'label' => 'Last name',
				'field_type' => 'Text',
				'required' => true,
				'show_index' => true
			],[
				'name' => 'email',
				'label' => 'Email',
				'field_type' => 'Email',
				'unique' => true,
				'maxlength' => '250',
				'required' => true,
				'show_index' => true
			],[
				'name' => 'country_phone_code',
				'label' => 'Country Phone Code',
				'field_type' => 'Hidden',
				'maxlength' => '5'
			],[
				'name' => 'phone_no',
				'label' => 'Phone Number',
				'field_type' => 'Phone',
				'unique' => true,
                'required' => true,
				'nullable_required' => true,
				'show_index' => true
			],[
				'name' => 'gender',
				'label' => 'Gender',
				'field_type' => 'Radio',
				'show_index' => true,
				'json_values' => ["Male","Female","Other"]
			],[
				'name' => 'date_of_birth',
				'label' => 'Date Of Birth',
				'field_type' => 'Date_picker',
				'nullable_required' => false,
			],[
				'name' => 'profile_pic',
				'label' => 'Profile Picture',
				'field_type' => 'Image',
			],[
				'name' => 'context',
				'label' => 'Context Type',
				'field_type' => 'Polymorphic_select',
				'json_values' => ["MasterUsers","Employees"]
			],[
				'name' => 'password',
				'label' => 'Password',
				'field_type' => 'Password',
                'required' => true,
				'nullable_required' => true,
			]
        ]);
        
        /*
        Module::generate('Users' 'users', 'last_name', 'fa-group', [
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
        if(Schema::hasTable('users')) {
            Schema::drop('users');
        }
    }
}
