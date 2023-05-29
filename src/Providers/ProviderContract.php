<?php

namespace Turbo124\BotLicker\Providers;

interface ProviderContract
{
    public function banIp(string $ip);

    public function unbanIp(string $ip);

    public function challengeIp(string $ip);

    public function unchallengeIp(string $ip);

    public function banCountry(string $iso_3166_2);

    public function unbanCountry(string $iso_3166_2);

    public function getRules();
}
