<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFightRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fight_records', function (Blueprint $table) {
            $table->dropColumn('question_id');
            $table->integer('fight_id')->unsigned()->change();
            $table->integer('user_id')->unsigned()->after('fight_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fight_records', function (Blueprint $table) {
            //
        });
    }
}
