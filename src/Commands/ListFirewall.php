<?php

namespace Turbo124\BotLicker\Commands;

use Illuminate\Console\Command;

class ListFirewall extends Command
{
    /**
     * @var string
     */
    protected $name = 'firewall:list';

    /**
     * @var string
     */
    protected $description = 'List banned IPs';

    public function handle()
    {
    }
}