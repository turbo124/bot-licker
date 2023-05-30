<?php

namespace Turbo124\BotLicker\Models;

use Illuminate\Database\Eloquent\Model;

class BotlickerBan extends Model
{
    public $timestamps = false;

    public $fillable = [
        'ip',
        'iso_3166_2',
        'expiry'
    ];
}
