<?php

namespace sndsgd\rate\limiter;

/**
 * A limiter implements logic to determine if any limits have been exceeded
 */
interface LimiterInterface
{
    /**
     * Increment all rate limits
     *
     * @param int $incrementBy The number to increment the limits by
     * @return \sndsgd\rate\limiter\LimiterInterface
     */
    public function increment(int $incrementBy = 1): LimiterInterface;

    /**
     * Determine if any rate limits have been exceeded
     *
     * @return bool
     */
    public function isExceeded(): bool;

    /**
     * Retrieve an array of rate limiter information for use in http headers
     *
     * @return array<string,string>
     */
    public function getHeaders(): array;

    /**
     * Retrieve an array of rate limit periods
     *
     * @return array<\sndsgd\rate\limit\Period>
     */
    public function getPeriods(): array;
}
