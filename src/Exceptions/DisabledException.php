<?php

namespace Turbo124\BotLicker\Exceptions;

use Exception;

class DisabledException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool
     */
    public function report(): bool
    {
        return false;
    }

}
