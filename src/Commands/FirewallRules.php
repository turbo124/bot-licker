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

        $collect_rules = collect($rules)->map(function ($r){
            
            return ['description' => $r['description'], 'expression' => $r['expression']];

        })->toArray();

        $this->table(
            ['Description', 'Expression'],
            $collect_rules
        );

    }
}
