<?php

use Illuminate\Database\Seeder;

class QuestionGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('question_group')->insert();
    }
}
