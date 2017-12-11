<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Helpers\Api\ApiResponse;
use App\Models\FightRecord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FightRecordController extends Controller
{
    use ApiResponse;

    protected $model;

    public function __construct(FightRecord $record)
    {
        $this->model = $record;
    }

    public function store(Request $request)
    {
        return $request->user()->fights()->find($request->fight_id);
    }
}
