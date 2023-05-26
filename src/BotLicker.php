<?php

namespace Turbo124\BotLicker;

use Turbo124\BotLicker\Providers\ProviderContract;
use Turbo124\BotLicker\Models\Botlicker as BotModel;

class BotLicker
{    
    /** @var ProviderContract $provider */
    protected ProviderContract $provider;
    
    /**
     * IP Address
     *
     * @var string
     */
    protected string $ip = '';
        
    /** 
     * ISO 3166 2 character country code
     *  
     * @var string $iso_3166_2 
     */
    protected string $iso_3166_2 = '';

    
    /** @var mixed $action */
    protected string $action = '';
    
    /**
     * Ban a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return self
     */
    public function ban(string $ip, array $params = []): self
    {
        $this->ip = $ip;
        $this->action = 'ban';

        $this->getProvider()->banIp($ip, $params);    

        return $this;

    }

    /**
     * Unban a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return self
     */
    public function unban(string $ip, array $params = []): self
    {
    
        $this->ip = $ip;

        $this->getProvider()->unbanIp($ip, $params);
    
        return $this;
    }
    
    /**
     * Challenge a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return self
     */
    public function challenge(string $ip, array $params = []): self
    {
        
        $this->ip = $ip;
        $this->action = 'challenge';

        $this->getProvider()->challengeIp($ip, $params);
    
        return $this;

    }
    
    /**
     * UnChallenge a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return self
     */
    public function unchallenge(string $ip, array $params = []): self
    {
        
        $this->ip = $ip;

        $this->getProvider()->unchallengeIp($ip, $params);
    
        return $this;

    }
    
    /**
     * Ban Country 
     *
     * @param  string $iso_3166_2
     * @param  array $params
     * @return self
     */
    public function banCountry(string $iso_3166_2, array $params = []): self
    {

        $this->iso_3166_2 = $iso_3166_2;

        $this->action = 'ban';

        $this->getProvider()->banCountry($iso_3166_2, $params);
    
        return $this;

    }

    /**
     * UnBan Country 
     *
     * @param  string $iso_3166_2
     * @param  array $params
     * @return self
     */
    public function unbanCountry(string $iso_3166_2, array $params = []): self
    {

        $this->iso_3166_2 = $iso_3166_2;

        $this->getProvider()->unbanCountry($iso_3166_2, $params);
    
        return $this;

    }

    public function getRules()
    {
        
        $this->getProvider()->getRules();

        return $this;

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
     * expires
     *
     * @param  \Illuminate\Support\Carbon $expiry
     * @return self
     */
    public function expires(?\Illuminate\Support\Carbon $expiry = null): self
    {
        
        BotModel::insert([
            'ip' => $this->ip,
            'iso_3166_2' => $this->iso_3166_2,
            'action' => $this->action,
            'expiry' => null
        ]);

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