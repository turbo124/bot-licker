<?php

namespace Turbo124\BotLicker\Commands;

use Illuminate\Console\Command;
use Turbo124\BotLicker\Models\BotlickerBan;

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
            BotlickerBan::all(['id', 'ip', 'iso_3166_2', 'expiry'])->toArray()
        );
    }
}
