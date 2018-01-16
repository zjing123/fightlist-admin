<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class Answer extends Model
{
    protected $fillable = [
        'title', 'question_id', 'score'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function entries()
    {
        return $this->hasMany(TranslationEntry::class, 'translation_id', 'title');
    }

    public static function getAnswers($question_id, $lang = 'zh_CN')
    {
        return DB::table('answers as a')
            ->leftJoin('translation_entries as t', 't.translation_id', '=', 'a.title')
            ->select('t.value as title','a.score')
            ->where('a.question_id', $question_id)
            ->where('t.lang', $lang)
            ->get();
    }
}
