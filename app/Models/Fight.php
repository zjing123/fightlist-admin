<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Fight extends Model
{
    protected $fillable = [
        'to_user_id', 'type', 'score', 'group_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function records()
    {
        return $this->hasMany(FightRecord::class, 'fight_id');
    }

    public function question()
    {
        return $this->belongsToMany(Question::class);
    }
}
