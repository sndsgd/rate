# sndsgd/rate

[![Latest Version](https://img.shields.io/github/release/sndsgd/rate.svg?style=flat-square)](https://github.com/sndsgd/rate/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/sndsgd/rate/LICENSE)
[![Build Status](https://img.shields.io/travis/sndsgd/rate/master.svg?style=flat-square)](https://travis-ci.org/sndsgd/rate)
[![Coverage Status](https://img.shields.io/coveralls/sndsgd/rate.svg?style=flat-square)](https://coveralls.io/r/sndsgd/rate?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/sndsgd/rate.svg?style=flat-square)](https://packagist.org/packages/sndsgd/rate)

Rate limiting for PHP.


## Requirements

This project is unstable and subject to changes from release to release.

You need **PHP >= 7.0** to use this library, however, the latest stable version of PHP is recommended.


## Install

Install `sndsgd/rate` using [Composer](https://getcomposer.org/).


## Usage

> Note: At the moment, this library only contains rate limiting tools.

```php
# define the rate limits
$clientIp = $di["request"]->getClientIp();
$limits = [
    new \sndsgd\rate\Limit("Search-PerSecond", $clientIp, 1, 3),
    new \sndsgd\rate\Limit("Search-PerHour", $clientIp, 600, 3600),
];

# create a limiter, and increment the hit counts for all limits
$limiter = new \sndsgd\rate\limiter\RedisLimiter($di["redis"], $limits);
$limiter->increment();

# copy the rate limit headers to the response
$response = $di["response"];
foreach ($limiter->getHeaders() as $header) {
    list($key, $value) = preg_split("/\:\s?/", $header, 2);
    $response->addHeader($key, $value);
}

# if the limit was exceeded, prevent futher execution
if ($limiter->isExceeded()) {
    throw new \sndsgd\exception\TooManyRequestsException();
}
```
