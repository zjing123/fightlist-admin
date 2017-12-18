<?php

namespace App\Http\Controllers\Api\V1;

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
        $groupId = 1;
        $lastFight = $request->user()->fights()->orderBy('group_id', 'desc')->first();
        if (!empty($lastFight)) {
            $groupId = $lastFight->group_id + 1;
        }

        if (!QuestionGroup::find($groupId)) {
            return $this->message('没有更多的问题了');
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
            return $this->message('参数错误！');
        }
        
        if (!$request->user()->isFight( $data['group'] ) ) {

        } else {
            return $this->message('发生错误', 'error');
        };
    }
}
