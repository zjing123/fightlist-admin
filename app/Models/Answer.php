<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;
use App\Models\Question;

class Answer extends Model
{
    protected $fillable = [
        'title', 'question_id', 'score'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
