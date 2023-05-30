
<p align="center">
    <img src="botlicker.png">
</p>

# BotLicker

## Ban / Challenge IPs/Countries at the network edge.

**The Problem**: After researching some laravel packages that provide firewalls, a common theme was that they all block at the application level.

This may work for sites with low levels of traffic, but when dealing with a large number of requests it becomes ineffective.

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
Firewall::ban('101.1.1.254', Carbon::now()->addYear());
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
Firewall::banCountry('DE', Carbon::now()->addYear());
```

To then disable

```php
Firewall::unbanCountry('DE');
```

### Protected IPs and Countries

If you need rules to whitelist IP address range or Countries simply...

```php
Firewall::whitelistIp('101.1.1.254');
```

or

```php
Firewall::whitelistCountry('US');
```

### Automated rules

Are you tired of seeing bots trying to hit .env or phpinfo.php in your logs? You can now zap these IPs instantly with, you can pass an option Carbon instance if you wish to have an expiry on the rule, otherwise it runs indefinitely.

```php
Rule::matches('phpinfo.php', now()->addYear())->ban();
```

### Extending BotLicker with new providers

Currently only Cloudflare is supported, however you can easily implement the ProviderContract and generate the corresponding methods for other WAFs. Once you have created your provider, simply inject it into the `setProvider()`

```php
Firewall::setProvider(OtherWAF::class)->ban('101.1.1.254', now()->addMinutes(5));
```

### TODO:
Currently the package perform simply bans/unbans etc. In the next iteration, ban duration will be also be added in order for some rules to be removed after X timeperiod. ie

```php
Firewall::ban('101.1.1.254', Carbon::now()->addYear());
```

## License
The MIT License (MIT)
