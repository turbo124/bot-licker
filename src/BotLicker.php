<?php

namespace Turbo124\BotLicker;

use Turbo124\BotLicker\Providers\ProviderContract;

class BotLicker
{    
    /** @var ProviderContract $provider */
    protected ProviderContract $provider;

    /**
     * Ban a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return bool
     */
    public function ban(string $ip, array $params = []): bool
    {
        return $this->getProvider()->banIp($ip, $params);
    }

    /**
     * Unban a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return bool
     */
    public function unban(string $ip, array $params = []): bool
    {
        return $this->getProvider()->unbanIp($ip, $params);
    }
    
    /**
     * Challenge a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return bool
     */
    public function challenge(string $ip, array $params = []): bool
    {
        return $this->getProvider()->challengeIp($ip, $params);
    }
    
    /**
     * UnChallenge a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return bool
     */
    public function unchallenge(string $ip, array $params = []): bool
    {
        return $this->getProvider()->unchallengeIp($ip, $params);
    }
    
    /**
     * Ban Country 
     *
     * @param  string $iso_3166_2
     * @param  array $params
     * @return bool
     */
    public function banCountry(string $iso_3166_2, array $params = []): bool
    {
        return $this->getProvider()->banCountry($iso_3166_2, $params);
    }

    /**
     * UnBan Country 
     *
     * @param  string $iso_3166_2
     * @param  array $params
     * @return bool
     */
    public function unbanCountry(string $iso_3166_2, array $params = []): bool
    {
        return $this->getProvider()->unbanCountry($iso_3166_2, $params);
    }

    /**
     * Set Provider
     *
     * @param  ProviderContract $provider
     * @return self
     */
    public function setProvider($provider): self
    {
        $this->provider = $provider;

        return $this;
    }
    
    /**
     * Get Provider
     *
     * @return ProviderContract
     */
    private function getProvider(): ProviderContract
    {
        $default_provider = config('bot-licker.provider');

        return $this->provider ?? new $default_provider;
    }

}