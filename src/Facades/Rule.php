<?php

namespace Turbo124\BotLicker\Facades;

use Illuminate\Support\Facades\Facade;

class Rule extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'bot-licker-rule';
    }
}
