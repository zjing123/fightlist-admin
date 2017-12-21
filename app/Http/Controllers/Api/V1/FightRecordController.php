<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Helpers\Api\ApiResponse;
use App\Models\FightRecord;
use App\Models\QuestionGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

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
        $fight = $request->user()->fights()->find($request->fight_id);
        if(!$fight->isCompleted() && !$fight->isRecord($request->question_id)) {
            $record = $fight->records()->create([
                'question_id' => $request->question_id,
                'answers' => serialize($request->answers),
                'score' => $this->getTotal($request->answers),
                'finished' => $request->finished
            ]);

            if ($record) {
                if ($fight->isCompleted()) {
                    $records = $fight->records()->get(['score']);
                    $total = 0;
                    foreach ($records as $record) {
                        $total += $record->score;
                    }

                    $fight->score = $total;
                    $fight->save();
                }

                return $this->success(['finished' => true]);
            }
        }

        return $this->success(['finished' => false]);
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
