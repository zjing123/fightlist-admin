<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Helpers\Api\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\Fight;
use Response;

class QuestionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return Fight::find(1)->isCompleted();
    }
}
