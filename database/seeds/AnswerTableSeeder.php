<?php

use Illuminate\Database\Seeder;

class AnswerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $answers = [];
        $answers1 = [
            '白菜',
            '萝卜',
            '青菜',
            '洋葱',
            '茄子',
            '西红柿',
            '黄瓜',
            '辣椒',
            '南瓜',
            '冬瓜',
            '洋葱',
            '芹菜',
            '丝瓜',
            '韭菜',
        ];
        foreach ($answers1 as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 1, 'score' => 1];
        }

        $answers2 =  [
            '苹果',
            '香蕉',
            '橘子',
            '桃',
            '桃子',
            '荔枝',
            '龙眼',
            '柑桔',
            '李子',
            '葡萄',
            '青梅',
            '椰子',
            '番石榴',
            '草莓',
            '梨',
            '木瓜',
            '芒果',
            '菠萝',
            '柠檬',
            '柿子',
            '柚子',
            '樱桃',
            '无花果',
            '猕猴桃',
            '水蜜桃'
        ];
        foreach ($answers2 as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 2, 'score' => 1];
        }

        $answers3 = [
            '虎',
            '狼',
            '鼠',
            '鹿',
            '貂',
            '猴',
            '貘',
            '树懒',
            '斑马',
            '狗',
            '狐',
            '熊',
            '象',
            '豹子',
            '麝牛',
            '狮子',
            '小熊猫',
            '疣猪',
            '羚羊',
            '驯鹿',
            '考拉',
            '犀牛',
            '猞猁',
            '穿山甲',
            '长颈鹿',
            '熊猫',
            '食蚁兽',
            '猩猩',
            '海牛',
            '水獭',
            '灵猫',
            '海豚',
            '海象',
            '鸭嘴兽',
            '刺猬',
            '北极狐',
            '无尾熊',
            '北极熊',
            '袋鼠',
            '犰狳',
            '河马',
            '海豹',
            '鲸鱼',
            '鼬'
        ];
        foreach ($answers3 as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 3, 'score' => 1];
        }

        DB::table('answers')->insert($answers);
    }
}
