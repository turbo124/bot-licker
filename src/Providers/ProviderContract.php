<?php

namespace Turbo124\BotLicker\Providers;

interface ProviderContract
{
    public function banIp(string $ip, array $params = []);

    public function unbanIp(string $ip, array $params = []);

    public function challengeIp(string $ip, array $params = []);

    public function unchallengeIp(string $ip, array $params = []);

    public function getIpInfo(string $ip, array $params = []);

    public function getIpList(string $zone, array $params = []);

    public function getIpCount(string $zone, array $params = []);

    public function banCountry(string $iso_3166_2, array $params = []);

    public function unbanCountry(string $iso_3166_2, array $params = []);

    public function getRules();
}
