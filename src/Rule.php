<?php

namespace Turbo124\BotLicker;

use Illuminate\Support\Carbon;
use Turbo124\BotLicker\Models\BotlickerRule;

class Rule
{
    private $protected_ips = [
        '127.0.0.1',
    ];

    private string $rule = '';

    private string $action = 'ban';

    private ?int $duration;

    public function matches(string $rule, ?int $expires = null)
    {

        if(strlen($rule) < 3)
            return $this;

        $this->rule = $rule;
        $this->duration = $expires ? now()->addSeconds($expires): null;

        return $this;
    }

    public function ban(): void
    {

        $this->persist('ban');

    }


    public function challenge(): void
    {

        $this->persist('challenge');
        
    }


    private function persist(string $action): void
    {

        BotlickerRule::on(config('bot-licker.db_connection'))
                    ->insert([
                        'matches'   => $this->rule,
                        'action'    => $action,
                        'expiry'    => $this->duration,
                    ]);

    }
}