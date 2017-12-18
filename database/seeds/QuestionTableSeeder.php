<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class QuestionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(QuestionGroupTableSeeder::class);

        $questions = [
            ['title' => '写出能想到的蔬菜的名称', 'group_id' => 1],
            ['title' => '写出能想到的水果的名称', 'group_id' => 1],
            ['title' => '写出常见的动物的名称', 'group_id' => 1]
        ];
        DB::table('questions')->insert($questions);

        $this->call(AnswerTableSeeder::class);
    }
}
