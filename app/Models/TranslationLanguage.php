<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class TranslationLanguage extends Model
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'locale', 'title',
    ];
}
