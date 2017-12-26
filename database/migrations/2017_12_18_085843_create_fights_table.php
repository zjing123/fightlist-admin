<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('to_user_id')->unsigned();
            $table->integer('type')->unsigned()->default(1);
            $table->integer('score');
            $table->integer('group_id')->unsigned();
            $table->index('user_id');
            $table->index('to_user_id');
            $table->index(['user_id', 'type']);
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
        Schema::dropIfExists('fights');
    }
}
