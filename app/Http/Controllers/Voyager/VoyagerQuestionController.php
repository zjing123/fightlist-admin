<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VoyagerQuestionController extends VoyagerBreadController
{
    public function create(Request $request)
    {
        return parent::create($request);
    }
}
