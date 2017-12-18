<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionGroup extends Model
{
    //
    public function questions()
    {
        return $this->hasMany(Question::class, 'group_id');
    }

    /**
     * 获取group下的question个数
     * @return int
     *
     */
    public function getQuestionCount()
    {
        $count = $this->questions()->count();
        return $count;
    }

    public function getGroup($userId)
    {

    }
}
