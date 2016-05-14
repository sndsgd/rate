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
     * The period instances created after incrementing are stored here
     *
     * @var array<\sndsgd\rate\limit\Period>
     */
    protected $periods = [];

    /**
     * @param array<\sndsgd\rate\Limit> $limits
     */
    public function __construct(array $limits)
    {
        if (empty($limits)) {
            throw new \InvalidArgumentException(
                "invalid value provided for 'limits'; expecting an array ".
                "with at least one instance of sndsgd\\rate\\LimitInterface"
            );
        }

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
        if (empty($this->periods)) {
            $this->increment();
        }

        $result = false;
        foreach ($this->periods as $period) {
            if ($period->isLimitExceeded()) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        $ret = [];
        foreach ($this->periods as $period) {
            $ret[] = $period->getHeader();
        }
        return $ret;
    }
}
