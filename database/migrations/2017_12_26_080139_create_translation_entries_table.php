<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translation_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('translation_id');
            $table->string('lang');
            $table->text('value');
            $table->timestamps();
            $table->index(['translation_id', 'lang']);
            $table->unique(['translation_id', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translation_entries');
    }
}
