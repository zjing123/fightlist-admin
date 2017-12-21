<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Question;
use App\Models\QuestionGroup;
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
        $fights = $request->user()->fights()->with('user')->take(5)->get();
        $fightings = $request->user()->fightings()->with('user')->get();
        return $this->success([
            'fights' => $fights,
            'fightings' => $fightings
        ]);
    }

    public function store(Request $request)
    {
        $usedGroupIds = $request->user()->fights()->pluck('group_id');
        $groupId = DB::table('question_groups')
            ->whereNotIn('id', $usedGroupIds)
            ->value('id');

        if (empty($groupId)) {
            return $this->error('没有更多的问题了');
        }

        //DB::connection()->enableQueryLog();
        //print_r(DB::getQueryLog());
        $questions = QuestionGroup::find($groupId)->questions()->get();
        foreach ($questions as &$question) {
            $question->answers = $question->answers()->get();
        }
        unset($question);//去掉引用

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
        }
    }

    public function show(Request $request)
    {
        $fightRecords = Fight::find((int)$request->fight)->records;
        if($fightRecords) {
            $results = [];
            foreach ($fightRecords as $record) {
                $result = [];
                $question = Question::find($record->question_id);
                if ($question) {
                    $result['id'] = $question->id;
                    $result['title'] = $question->title;
                    $result['answers'] = unserialize($record->answers);
                    $result['right'] = $question->answers()->get(['title', 'score']);
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
