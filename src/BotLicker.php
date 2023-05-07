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
     * @return void
     */
    public function ban(string $ip, array $params = [])
    {
        $this->getProvider()->banIp($ip, $params);
    }

    /**
     * Unban a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return void
     */
    public function unban(string $ip, array $params = [])
    {
        $this->getProvider()->unbanIp($ip, $params);
    }
    
    /**
     * Challenge a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return void
     */
    public function challenge(string $ip, array $params = [])
    {
        $this->getProvider()->challengeIp($ip, $params);
    }
    
    /**
     * UnChallenge a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return void
     */
    public function unchallenge(string $ip, array $params = [])
    {
        $this->getProvider()->unchallengeIp($ip, $params);
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