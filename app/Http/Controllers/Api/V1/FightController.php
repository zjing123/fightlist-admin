<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\TranslationEntry;
use Illuminate\Cache\Repository;
use Illuminate\Database\QueryException;
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

        $lang = $request->get('lang', 'zh_CN');
        $type = $request->get('type', 1);

        $usedGroupIds = DB::table('fights')
            ->leftJoin('fight_records', 'fights.id', '=', 'fight_records.fight_id')
            ->where('fight_records.user_id', $request->user()->id)
            ->where('fights.type', $type)
            ->pluck('group_id');

        $groupId = QuestionGroup::whereNotIn('id', $usedGroupIds)->value('id');
        if (empty($groupId)) {
            //取消限制
            $groupId = 1;
            //return $this->error('no more question');
        }

        $record = null;
        if ($type == 1) {
            DB::beginTransaction();
            try{
                $fight = Fight::create([
                    'group_id' => $groupId,
                    'type' => $type,
                    'count' => 1
                ]);

                $record = FightRecord::create([
                    'fight_id' => $fight->id,
                    'user_id' => $request->user()->id,
                    'lang' => $lang
                ]);

                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                return $this->error('create game failed');
            }
        } else if ($type == 2) {
            $fight = Fight::where([
                ['group_id', '=', $groupId],
                ['count', '<',  Fight::DEFAULT_COUNT],
                ['type', '=', $type]
            ])->first();

            DB::beginTransaction();
            try{

                if (empty($fight)) {
                    $fight = Fight::create([
                        'group_id' => $groupId,
                        'type' => $type,
                        'count' => 1
                    ]);
                } else {
                    //update count
                    $fight->count = $fight->count + 1;
                    $fight->save();
                }

                $record = FightRecord::create([
                    'fight_id' => $fight->id,
                    'user_id' => $request->user()->id,
                    'lang' => $lang
                ]);

                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                return $this->error('create game failed');
            }
        } else {
            return $this->error('failed params');
        }

        $questions = $questions = Question::getQuestionsByLang($groupId, $lang);

        $data = [
            'questions' => $questions,
            'record_id' => $record->id
        ];

        return $this->success($data);



      // print_r($questions);

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
        $fightUsers = Fight::find($request->fight)->records()->with('user')->get();
        if (empty($fightUsers)) {
            return $this->error('没有相关数据');
        }

        return $this->success(['results' => $fightUsers]);
    }

}
