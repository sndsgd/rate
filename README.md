# sndsgd/rate

At the moment, this library only contains rate limiting tools.

## Example Usage

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
    throw new \sndsgd\http\exception\TooManyRequestsException();
}
```
