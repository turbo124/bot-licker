<?php

namespace Turbo124\BotLicker\Tests;

use Illuminate\Support\Facades\Http;

class CloudflareTest extends BotLickerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetRules()
    {

        Http::fake([
            // Stub a series of responses for GitHub endpoints...
            'api.cloudflare.com/client/v4/*' => Http::sequence()
                                    ->push('Hello World', 200)
                                    ->push(['foo' => 'bar'], 200)
                                    ->pushStatus(404),
        ]);

    }
}

