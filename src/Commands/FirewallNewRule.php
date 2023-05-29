<?php

namespace Turbo124\BotLicker\Commands;

use Illuminate\Console\Command;
use Turbo124\BotLicker\BotLicker;
use Turbo124\BotLicker\Models\BotlickerRule;

class FirewallNewRule extends Command
{
    /**
     * @var string
     */
    protected $name = 'firewall:rule';

    /**
     * @var string
     */
    protected $description = 'Add Firewall Rule';

    public function handle()
    {

        $matches = $this->ask('URI match ie .env / phpinfo etc...'); 

        $action = $this->choice('Action', ['ban', 'challenge'], 0);

        $expires = $this->ask('Rule expiry (seconds) leave blank for never');

        BotlickerRule::on(config('bot-licker.db_connection'))->create([
            'matches' => $matches,
            'action' => $action,
            'expiry' => $expires ?? null
        ]);

        
        $this->info('Rule added');

    }
}
