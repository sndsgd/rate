<?php

namespace sndsgd\rate;

class Limit implements LimitInterface, \JsonSerializable
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
        $this->limit = max($limit, 1);
        $this->duration = max($duration, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Retreive the cache key to store the hit value in
     * The hash does NOT include the limit so the limit can be changed
     * without having an affect on the current count
     *
     * @return string
     */
    public function getHash(): string
    {
        return "{$this->name}|{$this->info}|{$this->duration}";
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
