<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Helpers\Api\ApiResponse;
use App\Models\FightRecord;
use App\Models\QuestionGroup;
use App\Models\Question;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FightRecordController extends Controller
{
    use ApiResponse;

    protected $model;

    public function __construct(FightRecord $record)
    {
        $this->model = $record;
    }

    public function index(Request $request)
    {

    }

    public function store(Request $request)
    {
        $lang = $request->lang;
        $record_id = $request->record_id;
        $result = $request->result;
        $score = $request->score;
        $finished = $request->finished;

        $record = FightRecord::find($record_id);
        if (empty($record)) {
            return $this->success(['finished' => false]);
        }

        DB::beginTransaction();
        try {
            $record->answers = serialize($result);
            $record->score = $score;
            $record->finished = $finished;
            $record->save();
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            return $this->error('Synchronization data failure');
        }

        return $this->success(['finished' => true]);
    }

    public function show(Request $request)
    {
        $fight = FightRecord::with('fight')->where('id', $request->fightrecord)->get()->first();

        if (empty($fight)) {
            return $this->error('没有找到记录');
        }

        $questions = Question::getQuestionsByLang($fight->fight->group_id, $fight->lang);

        $result =[
            'score' => $fight->score,
            'results' => unserialize($fight->answers),
            'rightResults' => $questions
        ];

        return $this->success($result);
    }


    protected function getTotal($answers)
    {
        $total = 0;
        foreach ($answers as $answer) {
            $total += $answer['score'];
        }

        return $total;
    }
}
