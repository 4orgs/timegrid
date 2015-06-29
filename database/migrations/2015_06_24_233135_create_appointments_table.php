<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('appointments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contact_id')->unsigned();
			$table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
			$table->integer('business_id')->unsigned();
			$table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
			$table->string('hash', 32)->unique();
			$table->enum('status', ['R','C', 'A', 'S']); // Reserved, Confirmed, Annulated, Served
			$table->timestamp('start_at')->index();
			$table->integer('duration')->nullable();
			$table->json('services')->nullable();
			$table->string('comments')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('appointments');
	}

}
