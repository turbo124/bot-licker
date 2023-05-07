<?php

namespace Turbo124\BotLicker\Commands;

use Illuminate\Console\Command;
use Turbo124\BotLicker\BotLicker;

class FirewallRules extends Command
{
    /**
     * @var string
     */
    protected $name = 'firewall:rules';

    /**
     * @var string
     */
    protected $description = 'List Firewall Rules';

    public function handle()
    {

        $bot = new BotLicker();
        $rules = $bot->getRules();

        echo print_r($bot->getRules(), true);

        $this->table(
            ['Name', 'Email'],
            [$rules['description'], $rules['expression']]
        );

    }
}
