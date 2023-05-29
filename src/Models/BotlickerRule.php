<?php

namespace Turbo124\BotLicker\Models;

use Illuminate\Database\Eloquent\Model;

class BotlickerRule extends Model
{

    public $timestamps = false;
    
    public $fillable = [
        'matches',
        'action',
        'expiry'
    ];
}
