<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFightRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fight_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fight_id');
            $table->integer('question_id');
            $table->text('answers');
            $table->integer('score')->default(0);
            $table->integer('finished')->default(0);
            $table->index('fight_id');
            $table->index(['fight_id', 'question_id']);
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
        Schema::dropIfExists('fight_records');
    }
}
