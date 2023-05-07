<?php

namespace Turbo124\BotLicker\Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Config;
use Turbo124\BotLicker\BotLicker;
use Turbo124\BotLicker\BotLickerServiceProvider;
use Turbo124\BotLicker\Facades\Firewall;

class ConfigTest extends \Orchestra\Testbench\TestCase
{

    public function setUp(): void
    {
        parent::setUp();

    }

    protected function getPackageProviders($app)
    {
        return [
        BotLickerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
        
        $filename = __DIR__."/../.env";
        if (! file_exists($filename)) {
            throw new \Exception("could not load env vars ".__DIR__);
        }

        $vars = array();
        if ($filename !== '') {
            $vars = parse_ini_file($filename);
            foreach ($vars as $varKey => $varValue) {
                $_ENV[$varKey] = $varValue;
            }
        }

        $app['config']->set('bot-licker.cloudflare.zone_id', $_ENV["CLOUDFLARE_ZONE_ID"]);
        $app['config']->set('bot-licker.cloudflare.email', $_ENV["CLOUDFLARE_EMAIL"]);
        $app['config']->set('bot-licker.cloudflare.api_key', $_ENV["CLOUDFLARE_API_KEY"]);


    }
    /** @test */
    public function testValidInstanceType()
    {
        $firewall = new Firewall();
        $this->assertTrue($firewall instanceof Firewall);
    }

    public function testAddIp()
    {

        $bot = new BotLicker();
        $bot->ban('101.55.31.114');

    }
}
