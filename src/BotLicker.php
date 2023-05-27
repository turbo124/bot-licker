<?php

namespace Turbo124\BotLicker;

use Illuminate\Support\Carbon;
use Turbo124\BotLicker\Models\BotlickerBan;
use Turbo124\BotLicker\Providers\ProviderContract;
use Turbo124\BotLicker\Exceptions\DisabledException;

class BotLicker
{    
    /** @var ProviderContract $provider */
    protected ProviderContract $provider;
    
    /**
     * IP Address
     *
     * @var string
     */
    protected ?string $ip;
        
    /** 
     * ISO 3166 2 character country code
     *  
     * @var string $iso_3166_2 
     */
    protected ?string $iso_3166_2;

    
    /** 
     * The action to perform on this IP / Country
     * 
     * @var mixed $action 
     * */
    protected string $action = '';
    
    /**
     * Ban a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return self
     */
    public function ban(string $ip, ?Carbon $expiry): self
    {
        $this->ip = $ip;

        $this->action = 'ban';

        try{

            $this->preFlight()->getProvider()->banIp($ip);    

            $this->expires($expiry);

            return $this;

        }
        catch(\Exception $e) {

        }


    }

    /**
     * Unban a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return self
     */
    public function unban(string $ip, ?Carbon $expiry): self
    {
    
        $this->ip = $ip;

        try {

            $this->preFlight()->getProvider()->unbanIp($ip);
    
            $this->expires($expiry);

            return $this;

        }
        catch(\Exception $e) {

        }

    }
    
    /**
     * Challenge a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return self
     */
    public function challenge(string $ip, ?Carbon $expiry): self
    {
        
        $this->ip = $ip;
        $this->action = 'challenge';

        try {
        
            $this->preFlight()->getProvider()->challengeIp($ip);
            
            $this->expires($expiry);

            return $this;

        }
        catch(\Exception $e) {

        }

    }
    
    /**
     * UnChallenge a IP address 
     *
     * @param  string $ip
     * @param  array $params
     * @return self
     */
    public function unchallenge(string $ip, ?Carbon $expiry): self
    {
        
        $this->ip = $ip;

        try {
        
            $this->preFlight()->getProvider()->unchallengeIp($ip);
        
            $this->expires($expiry);

            return $this;

        }
        catch(\Exception $e) {

        }


    }
    
    /**
     * Ban Country 
     *
     * @param  string $iso_3166_2
     * @param  array $params
     * 
     * @return self
     */
    public function banCountry(string $iso_3166_2, ?Carbon $expiry): self
    {

        $this->iso_3166_2 = $iso_3166_2;

        $this->action = 'ban';

        try {
            
            $this->preFlight()->getProvider()->banCountry($iso_3166_2);
    
            $this->expires($expiry);

            return $this;

        }
        catch(\Exception $e) {

        }

    }

    /**
     * UnBan Country 
     *
     * @param  string $iso_3166_2
     * @param  array $params
     * 
     * @return self
     */
    public function unbanCountry(string $iso_3166_2, ?Carbon $expiry): self
    {

        $this->iso_3166_2 = $iso_3166_2;

        try {

            $this->preFlight()->getProvider()->unbanCountry($iso_3166_2);
    
            $this->expires($expiry);

            return $this;

        }
        catch(\Exception $e) {

        }

    }
    
    /**
     * Get the existing rules that are in the WAF
     *
     * @return void
     */
    public function getRules(): mixed
    {
        try {

            $this->getProvider()->getRules();

            return $this;

        }
        catch(\Exception $e) {

        }

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
        
        BotlickerBan::on(config('bot-licker.db_connection'))
        ->insert([
            'ip' => $this->ip,
            'iso_3166_2' => $this->iso_3166_2,
            'action' => $this->action,
            'expiry' => $expiry
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
    
    /**
     * Pre Flight Checks
     *
     * @return self
     */
    private function preFlight(): self
    {
        if(config('bot-licker.enabled'))
            throw new DisabledException('Disabled package', 444);

        if($this->ip && in_array($this->ip, config('bot-licker.whitelist_ips')))
            throw new \Exception('Protected IP address, cannot be actioned', 400);

            
        if ($this->ip && in_array($this->ip, config('bot-licker.whitelist_countries'))) 
            throw new \Exception('Protected Country Code, cannot be actioned', 400);

        return $this;
    }

}