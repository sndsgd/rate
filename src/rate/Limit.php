<?php

namespace sndsgd\rate;

class Limit implements \JsonSerializable
{
    /**
     * A client facing name for the rate limiter
     * Used in `X-RateLimit` headers to describe the limit
     *
     * @var string
     */
    protected $name;

    /**
     * Unique information for the rate limit
     *
     * @var string
     */
    protected $info;

    /**
     * The number of hits allowed per quota period
     *
     * @var int
     */
    protected $limit;

    /**
     * The number of seconds per quota period
     *
     * @var int
     */
    protected $duration;

    /**
     * @param string $name
     * @param string $info
     * @param int $limit 
     * @param int $duration 
     */
    public function __construct(
        string $name,
        string $info,
        int $limit = 1,
        int $duration = 1
    ) {
        $this->name = $name;
        $this->info = $info;
        $this->limit = $this->verifyInt($limit, 'limit');
        $this->duration = $this->verifyInt($duration, 'duration');
    }

    /**
     * Ensure a value is an integer greater than 0
     * @param int $value
     * @param string $name
     * @return int
     * @throws \InvalidArgumentException
     */
    protected function verifyInt(int $value, string $name)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException(
               "invalid value provided for '$name'; ".
               "expecting an integer greater than 0"
            );
        }
        return $value;
    }

    /**
     * Get the header key for this particular rate limit
     *
     * @return string
     */
    public function getHeaderKey(): string
    {
        return "X-RateLimit-{$this->name}";
    }

    /**
     * Get the header value for this particular rate limit
     *
     * @param int $hits The number of hits towards the limit
     * @param int $ttl The number of seconds before the limit resets
     * @return string
     */
    public function getHeaderValue(int $hits = 0, int $ttl): string
    {
        return sprintf(
            "Limit: %d; Remaining-Hits: %d; Reset-In: %d",
            $this->limit,
            $this->getRemainingHits($hits),
            max($ttl, 1)
        );
    }

    /**
     * Get the number of hits remaining
     *
     * @param int $hits The number of hits recorded
     * @return int
     */
    protected function getRemainingHits(int $hits): int
    {
        $remainingHits = $this->limit - $hits;
        return max($remainingHits, 0);
    }

    /**
     * Retreive the cache key to store the hit value in
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        return implode("|", [
            $this->name,
            $this->info,
            $this->limit,
            $this->duration,
        ]);
    }

    /**
     * Get the array representation of the rate limit
     *
     * @return array<string,string|int>
     */
    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "limit" => $this->limit,
            "duration" => $this->duration,
        ];
    }

    /**
     * @return array<string,string|int>
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
