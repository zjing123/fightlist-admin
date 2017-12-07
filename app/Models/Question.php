<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Traits\Translatable;

class Question extends Model
{
    use Translatable;
}
