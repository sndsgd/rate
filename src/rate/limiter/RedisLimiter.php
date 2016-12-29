<?php

namespace sndsgd\rate\limiter;

class RedisLimiter extends LimiterAbstract
{
    /**
     * A redis connection resource
     *
     * @var \Redis
     */
    protected $redis;

    /**
     * @param \Redis $redis
     * @param array<\sndsgd\rate\LimitInterface> $limits
     */
    public function __construct(\Redis $redis, array $limits)
    {
        $this->redis = $redis;
        parent::__construct($limits);
    }

    /**
     * @inheritDoc
     */
    public function increment(int $incrementBy = 1): LimiterInterface
    {
        $incrementResults = $this->incrementKeys($incrementBy);

        $timeouts = [];
        foreach ($this->limits as $limit) {
            $hits = array_shift($incrementResults);
            $timeout = array_shift($incrementResults);
            if ($hits === 1) {
                $timeouts[] = $limit;
                $timeout = $limit->getDuration();
            }
            $period = new \sndsgd\rate\limit\Period($limit, $hits, $timeout);
            $this->periods[$limit->getHash()] = $period;
        }

        if ($timeouts) {
            $this->setTimeouts($timeouts);
        }
        return $this;
    }

    /**
     * Stubbable method for incrementing the keys
     *
     * @param int $incrementBy The number of hits to increment by
     * @return array<int> Alternating hit count and ttl seconds
     */
    protected function incrementKeys(int $incrementBy)
    {
        $pipe = $this->redis->multi(\Redis::PIPELINE);
        foreach ($this->limits as $limit) {
            $cacheKey = $limit->getHash();
            $pipe->incrBy($cacheKey, $incrementBy);
            $pipe->ttl($cacheKey);
        }
        return $pipe->exec();
    }

    /**
     * Stubbable method for setting timeouts on new keys
     *
     * @param array<\sndsgd\rate\LimitInterface> The limits to set timeouts for
     */
    protected function setTimeouts(array $limits)
    {
        $pipe = $this->redis->multi(\Redis::PIPELINE);
        foreach ($limits as $limit) {
            $pipe->setTimeout($limit->getHash(), $limit->getDuration());
        }
        $pipe->exec();
    }
}
