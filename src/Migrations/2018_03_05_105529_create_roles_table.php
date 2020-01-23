<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\FieldType;
use Sagartakle\Laracrud\Models\Field;
use Sagartakle\Laracrud\Models\Role;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Module::generate('Roles', 'roles', 'name', 'fa-user', [
            [
				'name' => 'name',
				'label' => 'Name',
				'field_type' => 'Text',
				'unique' => true,
				'defaultvalue' => Null,
				'minlength' => '0',
				'maxlength' => '0',
				'required' => true,
				'show_index' => true
			],[
				'name' => 'label',
				'label' => 'Label Name',
				'field_type' => 'Text',
				'unique' => false,
				'defaultvalue' => Null,
				'minlength' => '0',
				'maxlength' => '0',
				'required' => true,
				'show_index' => true
			],[
				'name' => 'context_type',
				'label' => 'Context Type',
				'field_type' => 'Select2',
				'unique' => false,
				'defaultvalue' => 'Employees',
				'minlength' => '0',
				'maxlength' => '0',
				'required' => false,
				'show_index' => true,
				'json_values' => ["Employees","MasterUsers","Doctors"]
			],[
				'name' => 'parent_id',
				'label' => 'Parent Role',
				'field_type' => 'Select2',
				'unique' => false,
				'defaultvalue' => Null,
				'minlength' => '0',
				'maxlength' => '0',
				'required' => true,
				'show_index' => true,
				'json_values' => '@Roles'
			]
        ]);
        
        /*
        Module::generate('Roles' 'roles', 'name', 'fa-user', [
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
        $role_super_admin = new Role;
        $role_super_admin->name = "Super_admin";
        $role_super_admin->label = "Super Admin";
        $role_super_admin->context_type = "Employees";
        $role_super_admin->parent_id = Null;
        $role_super_admin->save();

        $role_admin = new Role;
        $role_admin->name = "Admin";
        $role_admin->label = "Admin";
        $role_admin->context_type = "Employees";
        $role_admin->parent_id = $role_super_admin->id;
		$role_admin->save();
		
        $role_employee = new Role;
        $role_employee->name = "Employee";
        $role_employee->label = "Employee";
        $role_employee->context_type = "Employees";
        $role_employee->parent_id = $role_admin->id;
        $role_employee->save();

        $role_employee = new Role;
        $role_employee->name = "PartnerUser";
        $role_employee->label = "Master Users";
        $role_employee->context_type = "PartnerUsers";
        $role_employee->parent_id = $role_admin->id;
        $role_employee->save();

        $role_employee = new Role;
        $role_employee->name = "MasterUser";
        $role_employee->label = "Master Users";
        $role_employee->context_type = "MasterUsers";
        $role_employee->parent_id = $role_admin->id;
        $role_employee->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('roles')) {
            Schema::drop('roles');
        }
    }
}
