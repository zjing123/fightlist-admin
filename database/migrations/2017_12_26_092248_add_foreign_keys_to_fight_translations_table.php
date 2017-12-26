<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToFightTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fight_translations', function (Blueprint $table) {
            $table->foreign('entry_id')
                ->references('id')
                ->on('translation_entries')
                ->onUpdate('restrict')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fight_translations', function (Blueprint $table) {
            //
        });
    }
}
