<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FightRecord extends Model
{
    protected $fillable = [
        'question_id', 'answers', 'score', 'finished'
    ];

    public function fight()
    {
        return $this->belongsTo(Fight::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
