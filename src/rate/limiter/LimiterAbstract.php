<?php

namespace sndsgd\rate\limiter;

abstract class LimiterAbstract implements LimiterInterface
{
    /**
     * The rate limits to evaluate
     *
     * @var array<\sndsgd\rate\Limit>
     */
    protected $limits;

    /**
     * The number of hits for the rate limits
     *
     * @var int[]
     */
    protected $hits = [];

    /**
     * The ttls for the rate limits
     *
     * @var int[]
     */
    protected $ttls = [];

    /**
     * @param array<\sndsgd\rate\Limit> $limits
     */
    public function __construct(array $limits)
    {
        $this->limits = \sndsgd\TypeTest::typedArray(
            $limits,
            \sndsgd\rate\LimitInterface::class
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isExceeded(): bool
    {
        $result = false;
        for ($i = 0, $len = count($this->rateLimits); $i < $len; $i++) {
            $rateLimit = $this->rateLimits[$i];
            $hits = $this->hitCounts[$i];
            $remainingHits = $rateLimit->getRemainingHits($hits);
            if ($remainingHits < 1) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        $ret = [];
        foreach ($this->rateLimits as $rateLimit) {
            $ret[$rateLimit->getHeaderKey()] = $rateLimit->getHeaderValue();
        }
        return $ret;
    }
}
