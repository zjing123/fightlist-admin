<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
Use Illuminate\Support\Facades\DB;

class Fight extends Model
{
    const DEFAULT_COUNT = 10;

    protected $fillable = [
        'type', 'group_id', 'count'
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

    public static function getFight($user_id)
    {
        $usedGroupIds = DB::table('fights')
            ->leftJoin('fight_records', 'fights.id', '=', 'fight_records.fight_id')
            ->where('fight_records.user_id', $user_id)
            ->pluck('group_id');

        $groupId = QuestionGroup::whereNotIn('id', $usedGroupIds)->value('id');

        $fight = self::where([
            ['group_id', '=', $groupId],
            ['count', '<',  self::DEFAULT_COUNT]
        ])->get();

        return $fight;
    }
}
