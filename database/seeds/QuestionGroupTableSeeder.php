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
        for ($i = 1; $i <= 10; $i++) {
            DB::table('question_group')->insert([]);
        }
    }
}
