<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFightTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fight_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned();
            $table->string('locale');
            $table->text('value');
            $table->timestamps();
            $table->index(['entry_id', 'locale']);
            $table->unique(['entry_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fight_translations');
    }
}
