<?php

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
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('label');
            $table->string('table_name');
            $table->string('model');
            $table->string('controller');
            $table->string('represent_attr');
            $table->string('icon')->default("fa fa-smile");
        });
        
        // field_type
        Schema::create('field_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });
        
        \FieldType::insert([
            ["name" => "Address"],
            ["name" => "Checkbox"],
            ["name" => "Ckeditor"],
            ["name" => "Date_picker"],
            ["name" => "Date"],
            ["name" => "Datetime_picker"],
            ["name" => "Datetime"],
            ["name" => "Decimal"],
            ["name" => "Email"],
            ["name" => "Float"],
            ["name" => "File"],
            ["name" => "Files"],
            ["name" => "Hidden"],
            ["name" => "Image"],
            ["name" => "Json"],
            ["name" => "Link"],
            ["name" => "Month"],
            ["name" => "Multiselect"],
            ["name" => "Number"],
            ["name" => "Password"],
            ["name" => "Phone"],
            ["name" => "Polymorphic_select"],
            ["name" => "Polymorphic_multiple"],
            ["name" => "Currency"],
            ["name" => "Radio"],
            ["name" => "Select"],
            ["name" => "Select2_from_ajax"],
            ["name" => "Select2_multiple"],
            ["name" => "Select2_tags"],
            ["name" => "Select2_multiple_tags"],
            ["name" => "Select2"],
            ["name" => "Table"],
            ["name" => "Text"],
            ["name" => "Textarea"],
            ["name" => "Time"]
        ]);

        // fields
        Schema::create('fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('label');
            $table->integer('rank')->default(0);
            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('id')->on('modules');
            $table->unsignedBigInteger('field_type_id');
            $table->foreign('field_type_id')->references('id')->on('field_types');
            $table->boolean('unique')->default(false);
            $table->string('defaultvalue')->nullable()->default(Null);
            $table->unsignedBigInteger('minlength')->nullable()->default(0);
            $table->unsignedBigInteger('maxlength')->nullable()->default(0);
            $table->boolean('required')->default(false);
            $table->boolean('nullable_required')->default(true);
			$table->boolean('show_index')->default(false);
            $table->text('json_values')->nullable();
        });

        Schema::create('access_modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('assessor');
            $table->nullableMorphs('accessible');
            $table->enum('access', ['view', 'create','edit','deactivate']);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('label', 100);
            $table->string('link', 255)->nullable();
            $table->string('icon', 50)->default("fa-cube");
            $table->string('type', 20)->default("module");
            $table->integer('rank')->default(0);
            $table->unsignedBigInteger('parent')->nullable()->default(Null);
            $table->foreign('parent')->references('id')->on('menus')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('hierarchy')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('menus');
        Schema::dropIfExists('access_modules');
        Schema::dropIfExists('fields');
        Schema::dropIfExists('field_types');
        Schema::dropIfExists('modules');
    }
}
