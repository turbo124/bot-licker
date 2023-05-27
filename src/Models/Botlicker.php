<?php

namespace Turbo124\BotLicker\Models;

use Illuminate\Database\Eloquent\Model;

class Botlicker extends Model
{

    public $connection = "database.".config('bot-licker.db_connection');

}
