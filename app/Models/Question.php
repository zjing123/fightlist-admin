<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;
use App\Models\Answer;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class Question extends Model
{
    use Translatable;

    protected $fillable = [
        'title', 'group_id'
    ];

    public function group()
    {
        return $this->belongsTo(QuestionGroup::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function entries()
    {
        return $this->hasMany(TranslationEntry::class, 'translation_id', 'title');
    }

    public function fightRecords()
    {
        return $this->belongsToMany(FightRecord::class);
    }

    public function createQuestions(array $data)
    {
        
    }

    public static function getQuestionsByLang($group_id, $lang = 'zh_CN')
    {
        $questions = DB::table('questions as q')
            ->leftJoin('translation_entries as t', 't.translation_id', '=', 'q.title')
            ->select('q.id', 't.value as title')
            ->where('t.lang', $lang)
            ->where('questions.group_id', $group_id)
            ->get();

        foreach ($questions as &$question) {
            $question->answers = DB::table('answers as a')
                ->leftJoin('translation_entries as t', 't.translation_id', '=', 'a.title')
                ->select('a.id', 't.value as title', 'a.score')
                ->where('a.question_id', $question->id)
                ->where('t.lang', $lang)
                ->get();
        }
        unset($question);

        return $questions;
    }

    public static function get($id, $lang = 'zh_CN')
    {
        return DB::table('questions as q')
            ->leftJoin('translation_entries as t', 't.translation_id', '=', 'q.title')
            ->select('q.*', 't.value as title')
            ->where('t.lang', $lang)
            ->where('q.id', $id)
            ->first();
    }
}
