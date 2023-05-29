<?php

namespace Turbo124\BotLicker\Tests;

use Orchestra\Testbench\TestCase;
use Turbo124\BotLicker\BotLickerServiceProvider;

class BotlickerTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan(
            'migrate',
            ['--database' => 'testbench']
        )->run();

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

        $app['config']->set('bot-licker.enabled', true);
        $app['config']->set('bot-licker.db_connection', 'testbench');
        $app['config']->set('bot-licker.cloudflare.zone_id', $_ENV["CLOUDFLARE_ZONE_ID"]);
        $app['config']->set('bot-licker.cloudflare.account_id', $_ENV["CLOUDFLARE_ACCOUNT_ID"]);
        $app['config']->set('bot-licker.cloudflare.email', $_ENV["CLOUDFLARE_EMAIL"]);
        $app['config']->set('bot-licker.cloudflare.api_key', $_ENV["CLOUDFLARE_API_KEY"]);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $this->assertNotNull(config('bot-licker.enabled'));
    }

}