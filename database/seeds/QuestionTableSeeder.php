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
        //$this->call(QuestionGroupTableSeeder::class);

        $questions = [
            ['title' => '写出能想到的蔬菜的名称', 'group_id' => 1],
            ['title' => '写出能想到的水果的名称', 'group_id' => 1],
            ['title' => '写出常见的动物的名称', 'group_id' => 1],
            ['title' => '书桌上会有什么?', 'group_id' => 2],
            ['title' => '写出你能想到的电子产品', 'group_id' => 2],
            ['title' => '写出能想到的家电的名称', 'group_id' => 3],
            ['title' => '写出中国的男明星的名称', 'group_id' => 4],
            ['title' => '写出中国的女明星的名称', 'group_id' => 4],
            ['title' => '列出所知道的大学学科', 'group_id' => 4],
            ['title' => '列出唐朝皇帝的名称', 'group_id' => 4],
        ];
        DB::table('questions')->insert($questions);

        $this->call(AnswerTableSeeder::class);
    }
}
