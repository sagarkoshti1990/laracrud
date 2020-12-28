<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_log', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->nullableMorphs('user');
			$table->nullableMorphs('context');
			$table->string('action', 32)->nullable();
			$table->string('description')->nullable();
			$table->text('data')->nullable();
			$table->boolean('public');
			$table->string('ip_address', 64);
			$table->string('user_agent');
			$table->nullableTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('activity_log');
	}

}