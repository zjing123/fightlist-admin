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
        $list = [
            '白菜', '萝卜', '青菜', '洋葱', '茄子', '西红柿', '黄瓜', '辣椒', '南瓜', '冬瓜', '洋葱', '芹菜', '丝瓜', '韭菜',
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 1, 'score' => 1];
        }

        $list =  [
            '苹果', '香蕉', '橘子', '桃', '桃子', '荔枝', '龙眼', '柑桔', '李子', '葡萄', '青梅', '椰子', '番石榴', '草莓',
            '梨', '木瓜', '芒果', '菠萝', '柠檬', '柿子', '柚子', '樱桃', '无花果', '猕猴桃', '水蜜桃'
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 2, 'score' => 1];
        }

        $list = [
            '虎', '狼', '鼠', '鹿', '貂', '猴', '貘', '树懒', '斑马', '狗', '狐', '熊', '象', '豹子', '麝牛', '狮子', '小熊猫',
            '疣猪', '羚羊', '驯鹿', '考拉', '犀牛', '猞猁', '穿山甲', '长颈鹿', '熊猫', '食蚁兽', '猩猩', '海牛', '水獭', '灵猫', '海豚',
            '海象', '鸭嘴兽', '刺猬', '北极狐', '无尾熊', '北极熊', '袋鼠', '犰狳', '河马', '海豹', '鲸鱼', '鼬'
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 3, 'score' => 1];
        }

        $list = [
            '钢笔', '铅笔', '橡皮擦', '书', '纸', '墨水', '文具'
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 4, 'score' => 1];
        }

        $list = [
            '电脑', '电视', '鼠标', '手机', '收音机', '相机', '摄像机', '照相机', '打印机', '手表', '收录机', '激光唱片'
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 5, 'score' => 1];
        }

        $list = [
            '饮水机', '冰箱', '电脑', '洗衣机', '洗碗机', '吸油烟机', '燃气灶', '电磁炉', '电饭煲', '电压力锅', '消毒柜', '洗碗机',
            '榨汁机', '酸奶机', '豆浆机', '电火锅', '咖啡机', '电烤箱', '电饼铛', '多士炉', '面包机', '煮蛋器', '打蛋器', '电炒锅',
            '冰淇淋机', '果蔬消毒机'
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 6, 'score' => 1];
        }

        $list = [
            '陈赫', '陈坤','邓超','杜淳','冯绍峰','韩庚','胡歌','何炅','黄渤','黄晓明','贾乃亮','李晨','李易峰','鹿晗','井柏然',
            '刘烨','陆毅','孙红雷','佟大为','薛之谦','王宝强','汪峰','王俊凯','王凯','王源','魏晨','文章','吴亦凡','小沈阳',
            '徐峥','杨洋','张翰','张杰','张艺兴','郑恺'
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 7, 'score' => 1];
        }

        $list = [
            '白百何','董洁','霍思燕','范冰冰','高圆圆','李冰冰','李湘','李小璐','李宇春','蒋勤勤','蒋欣','刘诗诗','刘涛','柳岩',
            '刘亦菲','那英','戚薇','宋佳','孙俪','汤唯','唐嫣','佟丽娅','王珞丹','谢娜','杨幂','杨紫','姚晨','章子怡','赵丽颖',
            '赵薇','郑爽','周迅'
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 8, 'score' => 1];
        }

        $list = [
            '哲学','经济学','法学','教育学','文学','历史学','理学','工学','农学','医学','军事学','管理学','艺术学'
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 9, 'score' => 1];
        }

        $list = [
            '李渊','李世民','李治','李显','李旦','武曌','李显','李重茂','李旦','李隆基','李亨','李豫','李适','李诵','李纯',
            '李恒','李湛','李昂','李炎','李忱','李漼','李儇','李晔','李柷'
        ];
        foreach ($list as $answer) {
            $answers[] = ['title' => $answer, 'question_id' => 10, 'score' => 1];
        }

        DB::table('answers')->insert($answers);
    }
}
