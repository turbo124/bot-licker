<?php

namespace Turbo124\BotLicker\Commands;

use Illuminate\Console\Command;
use Turbo124\BotLicker\BotLicker;
use Turbo124\BotLicker\Models\BotlickerRule;

class FirewallShow extends Command
{
    /**
     * @var string
     */
    protected $name = 'firewall:show';

    /**
     * @var string
     */
    protected $description = 'Show actioned IP/Countries';

    public function handle()
    {
        $this->table(
            ['id', 'ip', 'country', 'expiry'],
            BotlickerRule::all(['id', 'ip', 'iso_3166_2', 'expiry'])->toArray()
        );
    }
}
