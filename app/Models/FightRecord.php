<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FightRecord extends Model
{
    protected $fillable = [
        'fight_id','user_id', 'answers', 'score', 'finished', 'lang'
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
