<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\TranslationEntry;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Fight;
use App\Models\FightRecord;
use Illuminate\Support\Facades\DB;
use App\Api\Helpers\Api\ApiResponse;

class FightController extends Controller
{
    use ApiResponse;

    protected $model;

    public function __construct(Fight $fight)
    {
        $this->model = $fight;
    }

    public function index(Request $request)
    {
        $fights = $request->user()->fights();
        return $this->success([
            'fights' => $fights
        ]);
    }

    public function store(Request $request)
    {

        DB::connection()->enableQueryLog();
        $result = DB::table('questions')
            ->leftJoin('translation_entries', 'translation_entries.translation_id', '=', 'questions.title')
            ->where('translation_entries.translation_id', '0dcd93b4-3951-4847-a2a9-82e4994edc18')
            ->where(function ($query) {
                $query->select('count(*)')
                    ->from(with(new TranslationEntry)->getTable())
                    ->where('translation_entries.translation_id', '0dcd93b4-3951-4847-a2a9-82e4994edc18');
            }, '>', 1)
            ->get();

            print_r(DB::connection()->getQueryLog());

        /*$usedGroupIds = $request->user()->fights()->pluck('group_id');
        $groupId = DB::table('question_groups')
            ->whereNotIn('id', $usedGroupIds)
            ->value('id');

        if (empty($groupId)) {
            return $this->error('没有更多的问题了');
        }

        $lang = $request->get('lang', 'zh_CN');

//        DB::connection()->enableQueryLog();
//
//        $questions = QuestionGroup::find($groupId)->questions()->get();
//        foreach ($questions as $question) {
//            $question->title = $question->entries;
//        }

        $questions = Question::getQuestionsByLang($groupId, $lang);

//        foreach ($questions as &$question) {
//            $question->answers = $question->answers()->get();
//        }
//        unset($question);//去掉引用
//
        $data = [
            'questions' => $questions,
            'group' => $groupId
        ];

        if (!$request->user()->isFight( $data['group'] ) ) {
            $fight = $request->user()->fights()->create( [
                'to_user_id' => $request->to_user_id || 0,
                'type' => $request->type,
                'group_id' => $data['group'],
                'score' => 0
            ]);

            if ( $fight->id ) {
                $data['fight_id'] = $fight->id;
                return $this->success($data);
            } else {
                return $this->error('参数错误！');
            }
        } else {
            return $this->error('发生错误');
        }*/
    }

    public function show(Request $request)
    {
        $fightRecords = Fight::find((int)$request->fight)->records;
        if($fightRecords) {
            $results = [];
            foreach ($fightRecords as $record) {
                $result = [];

                $question = Question::get($record->question_id);
                if ($question) {
                    $result['id'] = $question->id;
                    $result['title'] = $question->title;
                    $result['answers'] = unserialize($record->answers);
                    $result['right'] = Answer::getAnswers($question->id);
                    $results[] = $result;
                } else {
                    continue;
                }
            }

            return $this->success(['results' => $results]);
        } else {
            return $this->error('没有找到记录');
        }
    }

}
