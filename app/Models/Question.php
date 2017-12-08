<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;
use App\Models\Answer;

class Question extends Model
{
    use Translatable;

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
