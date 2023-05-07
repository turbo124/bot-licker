<?php

namespace Turbo124\BotLicker\Tests;

use PHPUnit\Framework\TestCase;
use Turbo124\BotLicker\Facades\Firewall;

class ConfigTest extends TestCase
{
    /** @test */
    public function testValidInstanceType()
    {
        $firewall = new Firewall();
        $this->assertTrue($firewall instanceof Firewall);
    }
}
