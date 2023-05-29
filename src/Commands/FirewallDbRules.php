<?php

namespace Turbo124\BotLicker\Commands;

use Illuminate\Console\Command;
use Turbo124\BotLicker\BotLicker;
use Turbo124\BotLicker\Models\BotlickerRule;

class FirewallDbRules extends Command
{
    /**
     * @var string
     */
    protected $name = 'firewall:waf {--delete=}';

    /**
     * @var string
     */
    protected $description = 'List Firewall Rules';

    public function handle()
    {
        
        if($this->option('delete')){
            BotlickerRule::destroy($this->option('delete'));
        }

        $this->table(
            ['id', 'Matches', 'Action', 'Expiry'],
            BotlickerRule::all(['id', 'matches', 'action', 'expiry'])->toArray()
        );

    }
}
