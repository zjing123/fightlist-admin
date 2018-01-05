<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Fight extends Model
{
    protected $fillable = [
        'to_user_id', 'type', 'score', 'group_id'
    ];

    public function records()
    {
        return $this->hasMany(FightRecord::class, 'fight_id');
    }

    public function isCompleted()
    {
        $questionCount = Question::where('group_id', $this->group_id)->count();
        $finishedCount = $this->records()->count();

        return $questionCount === $finishedCount;
    }

    public function isRecord($question_id)
    {
        return $this->records->contains('question_id', $question_id);
    }
}
