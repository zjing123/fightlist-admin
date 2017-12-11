<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Models\Fight;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($username)
    {
        return self::where('name', $username)->first();
    }

    public function fights()
    {
        return $this->hasMany(Fight::class);
    }

    public function fightRecords()
    {
        return $this->hasMany(FightRecord::class);
    }

    public function fight($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }

        $this->fights();
    }

    public function isFight($group_id)
    {
        return $this->fights->contains('group_id', $group_id);
    }
}
