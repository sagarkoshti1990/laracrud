<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('assessor_id')->nullable();
            $table->string('assessor_type')->nullable();
            $table->index(['assessor_id', 'assessor_type']);
            
            $table->string('accessible_id')->nullable();
            $table->string('accessible_type')->nullable();
            $table->index(['accessible_id', 'accessible_type']);
            $table->enum('access', ['view', 'create','edit','deactivate']);
            $table->softDeletes();
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
        Schema::dropIfExists('access_modules');
    }
}
