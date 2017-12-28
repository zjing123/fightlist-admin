<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class TranslationEntry extends Model
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'translation_id', 'lang', 'value'
    ];
}
