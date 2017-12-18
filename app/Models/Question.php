<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;
use App\Models\Answer;

class Question extends Model
{
    use Translatable;

    public function group()
    {
        return $this->belongsTo(QuestionGroup::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function fightRecords()
    {
        return $this->belongsToMany(FightRecord::class);
    }

    public function getNewQuestion()
    {
        DB::table('question_group')
            ->leftJoin('fights', 'question_group.id', '=', 'fights.group_id')
            ->where('fights.group_id is null')
            ->task(1)
            ->get();
    }
}
