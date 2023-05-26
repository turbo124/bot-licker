<?php

namespace Turbo124\BotLicker;

class Rule
{
    private $protected_ips = [
        '127.0.0.1',
    ];

    public string $ip = '';

    public function matches(string $ip)
    {

    }

    public function ban()
    {

    }
}