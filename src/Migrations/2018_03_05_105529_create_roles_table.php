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
        Module::generate('Roles', 'roles', 'name', 'fa fa-user-circle', [
            [
				'name' => 'name',
				'label' => 'Name',
				'field_type' => 'Text',
				'unique' => true,
				'required' => true,
				'show_index' => true
			],[
				'name' => 'label',
				'label' => 'Label Name',
				'field_type' => 'Text',
				'required' => true,
				'show_index' => true
			],[
				'name' => 'context_type',
				'label' => 'Context Type',
				'field_type' => 'Select2',
				'defaultvalue' => 'Employees',
				'show_index' => true,
				'json_values' => ["Employees","Users"]
			],[
				'name' => 'parent_id',
				'label' => 'Parent Role',
				'field_type' => 'Select2',
				'required' => true,
				'show_index' => true,
				'json_values' => '@Roles'
			]
        ],[
            'model' => config('stlc.role_model'),
            'controller' => \Sagartakle\Laracrud\Controllers\RolesController::class
        ]);
		
        Schema::create('rollables', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedBigInteger('role_id');
			$table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
			$table->morphs('rollable');
        });
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
			Ckeditor,
			Currency,
			Date,
			Date_picker,
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
		
        $role_super_admin = new Role;
        $role_super_admin->name = "Admin";
        $role_super_admin->label = "Admin";
        $role_super_admin->context_type = "Employees";
        $role_super_admin->parent_id = Null;
        $role_super_admin->save();
        
        $role_super_admin = new Role;
        $role_super_admin->name = "Users";
        $role_super_admin->label = "Users";
        $role_super_admin->context_type = "Users";
        $role_super_admin->parent_id = $role_super_admin->id;
        $role_super_admin->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rollables');
        Schema::dropIfExists('roles');
    }
}
