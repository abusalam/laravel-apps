<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('school_id');
            $table->unsignedInteger('old_school_id')->nullable();
            $table->string('teacher_name');
            $table->date('born_on');
            $table->enum('gender', ['MALE','FEMALE','OTHER']);
            $table->date('joined_on');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('old_school_id')->references('id')->on('schools');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teachers');
    }
}
