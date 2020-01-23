<?php

use Sagartakle\Laracrud\Models\FieldType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('label');
            $table->string('table_name');
            $table->string('model');
            $table->string('controller');
            $table->string('represent_attr');
            $table->string('icon')->default("fa-user");
        });
        
        // field_type
        Schema::create('field_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
        
        FieldType::create(["name" => "Address"]);
        FieldType::create(["name" => "Checkbox"]);
        FieldType::create(["name" => "CKEditor"]);
        FieldType::create(["name" => "Color_picker"]);
        FieldType::create(["name" => "Color"]);
        FieldType::create(["name" => "Date_picker"]);
        FieldType::create(["name" => "Date_range"]);
        FieldType::create(["name" => "Date"]);
        FieldType::create(["name" => "Datetime_picker"]);
        FieldType::create(["name" => "Datetime"]);
        FieldType::create(["name" => "Email"]);
        FieldType::create(["name" => "File"]);
        FieldType::create(["name" => "Files"]);
        FieldType::create(["name" => "Hidden"]);
        FieldType::create(["name" => "Icon_picker"]);
        FieldType::create(["name" => "Image"]);
        FieldType::create(["name" => "Json"]);
        FieldType::create(["name" => "Month"]);
        FieldType::create(["name" => "Multiselect"]);
        FieldType::create(["name" => "Number"]);
        FieldType::create(["name" => "Password"]);
        FieldType::create(["name" => "Phone"]);
        FieldType::create(["name" => "Currency"]);
        FieldType::create(["name" => "Radio"]);
        FieldType::create(["name" => "Select"]);
        FieldType::create(["name" => "Select2_from_ajax"]);
        FieldType::create(["name" => "Select2_multiple"]);
        FieldType::create(["name" => "Select2_tags"]);
        FieldType::create(["name" => "Select2_multiple_tags"]);
        FieldType::create(["name" => "Select2"]);
        FieldType::create(["name" => "Table"]);
        FieldType::create(["name" => "Text"]);
        FieldType::create(["name" => "Textarea"]);

        // fields
        Schema::create('fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('label');
            $table->integer('module_id')->unsigned();
            $table->foreign('module_id')->references('id')->on('modules');
            $table->integer('field_type_id')->unsigned();
            $table->foreign('field_type_id')->references('id')->on('field_types');
            $table->boolean('unique')->default(false);
            $table->string('defaultvalue');
            $table->integer('minlength')->unsigned()->default(0);
            $table->integer('maxlength')->unsigned()->default(0);
            $table->boolean('required')->default(false);
            $table->boolean('nullable_required')->default(true);
			$table->boolean('show_index')->default(false);
            $table->text('json_values');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fields');
        Schema::dropIfExists('field_types');
        Schema::dropIfExists('modules');
    }
}