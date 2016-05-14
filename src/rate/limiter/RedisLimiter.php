<?php

namespace sndsgd\rate\limiter;

class RedisLimiter extends LimiterAbstract
{
    /**
     * {@inheritdoc}
     */
    public function increment(int $incrementBy = 1): LimiterInterface
    {
        if (count($this->rateLimits) > 1) {
            return $this->incrementMultiple();
        }

        $limit = $this->limits[0];
        $cacheKey = $limit->getCacheKey();

        # use a pipeline to increment and retrieve the ttl in one shot
        list($hits, $ttl) = $redis
            ->multi(\Redis::PIPELINE)
            ->incrBy($cacheKey, $incrementBy)
            ->ttl($cacheKey)
            ->exec();

        if ($ttl === -1) {
            $result = $redis->setTimeout($cacheKey, $limit->getDuration());
        }

        $this->hits[0] = $hits;
        $this->ttls[0] = $ttl;

        return $this;
    }

    /**
     * Increment multiple limits
     *
     * @return \sndsgd\rate\limit\LimiterInterface
     */
    protected function incrementMultiple(): LimiterInterface
    {

    }




        # create a pipeline for incrementing and retrieving the ttl
        $pipe = $redis->multi(\Redis::PIPELINE);


        # create a pipeline for incrementing all limits in one request
        $pipe = $redis->multi(\Redis::PIPELINE);
        for ($i = 0; $i < $count; $i++) {
            $rateLimit = $this->rateLimits[$i];
            $cacheKey = $rateLimit->getCacheKey();
            $pipe->incr($cacheKey);
            $pipe->ttl($cacheKey);
        }
        $results = $pipe->exec();

        $timeouts = [];
        for ($i = 0, $i < $count; $i++) {
            $this->hits[$i] = $results[$i];
            $this->ttls[$i] = array_splice($results, $i, 1);
            if ($this->ttls[$i] === -1) {
                $timeouts[] = $this->rateLimits[$i];
            }
        }

        # if ttls need to be set batch them into a single request
        if ($timeouts) {
            $pipe = $redis->multi(\Redis::PIPELINE);
            foreach ($timeouts as $rateLimit) {
                $cacheKey = $rateLimit->getCacheKey();
                $duration = $rateLimit->getDuration();
                $pipe->setTimeout($cacheKey, $duration);
            }

            # verify the timeouts were set
            # if
            foreach ($pipe->exec() as $setTimeoutResult) {
                if (!$setTimeoutResult) {

                } else {
                    
                }
            }
        }
    }
}


