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
        if ($request->user()->isFight( $request->group ) ) {
            $fight = $request->user()->fights()->create( $request->only($this->model->fillable) );

            if ( $fight->id ) {
                return $this->success(['fight_id' => $fight->id]);
            } else {
                return $this->message('参数错误！');
            }
        } else {
            return $this->message('发生错误', 'error');
        };
    }
}
