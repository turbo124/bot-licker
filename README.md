
<p align="center">
    <img src="botlicker.png">
</p>

# BotLicker

## Ban / Challenge IPs/Countries at the network edge.

**The Problem**: After researching some laravel packages that provide firewall functionality, a common theme was that they all block at the application level.

This may work for sites with low levels of traffic, but when dealing with a large number of requests it becomes ineffective. It also continues to use resources unnecessarily.

Enter BotLicker where you can send your WAF rules direct to Cloudflare for instant implementation.

### Installation

```
composer require turbo124/bot-licker
```

### Configuration

In your .env file, enter in the following environment variables:

```
CLOUDFLARE_API_KEY="your_cloudflare_API_KEY_that_has_permission to read/write/edit WAF rules"
CLOUDFLARE_EMAIL="your_cloudflare_email_address"
CLOUDFLARE_ZONE_ID="your_zone_id"
CLOUDFLARE_ACCOUNT_ID="your_account_id"
```

### Usage

To permanently ban an IP address from reaching your server.

```php
Firewall::ban('101.1.1.254');
```

To ban an IP address for a certain period of time, simply pass a Carbon instance as the second argument
```php
Firewall::ban('10.1.1.1', Carbon::now()->addYear();
```

To unban and IP address

```php
Firewall::unban('101.1.1.254');
```

If you would prefer to issue a challenge to an IP address

```php
Firewall::challenge('101.1.1.254', Carbon::now()->addYear());
```

To then disable

```php
Firewall::unchallenge('101.1.1.254');
```

If you would prefer to ban an entire country simply pass in the iso_3166_2 country code

```php
Firewall::banCountry('DE');
```

To then disable

```php
Firewall::unbanCountry('DE');
```

### Protected IPs and Countries

If you need rules to whitelist IP address range or Countries simply...

These can be added to the configuration file bot-licker.php

### Automated rules

Are you tired of seeing bots trying to hit .env or phpinfo.php in your logs? You can build a custom ruleset which matches a string in the request URI. And then perform an action on the user arriving from this IP (or Country).

```php
Rule::matches('phpinfo.php', now()->addYear())->ban();
```

This will match any incoming request URIs with phpinfo.php in the URL ie https://domain.com/phpinfo.php and then ban the IP address for a year.

### Extending BotLicker with new providers

Currently only Cloudflare is supported, however you can easily implement the ProviderContract and generate the corresponding methods for other WAFs. Once you have created your provider, simply inject it into the `setProvider()` or replace the default provider in the configuration file.

```php
Firewall::setProvider(OtherWAF::class)->ban('101.1.1.254', now()->addMinutes(5));
```

## Console commands

If you prefer to ban from the console you can use these commands:

- Firewall rules:

```
php artisan firewall:rule
```

- Delete rule from WAF

```
php artisan firewall:waf --delete=
```

- Ban from the command line:

```
firewall:cf-rules {--ban} {--challenge} {--unban} {--unchallenge}
```

```
php artisan cf-rules --ban=10.1.1.1
```

- Show bans

```
php artisan firewall:show
```

### TODO:
Currently the package perform simply bans/unbans etc. In the next iteration, ban duration will be also be added in order for some rules to be removed after X timeperiod. ie

## License
The MIT License (MIT)
