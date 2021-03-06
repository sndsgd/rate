<?php

namespace sndsgd\rate\limit;

/**
 * A limit and details regarding progress towards it
 */
class Period
{
    /**
     * The limit
     *
     * @var \sndsgd\rate\LimitInterface
     */
    protected $limit;

    /**
     * The number of hits counted toward the limit
     *
     * @var int
     */
    protected $hits;

    /**
     * The number of seconds unitl the period resets
     *
     * @var int
     */
    protected $timeout;

    /**
     * @param \sndsgd\rate\LimitInterface $limit
     * @param int $hits
     * @param int $timeout
     */
    public function __construct(
        \sndsgd\rate\LimitInterface $limit,
        int $hits = 0,
        int $timeout = 1
    ) {
        $this->limit = $limit;
        $this->hits = $hits;
        $this->timeout = $timeout;
    }

    /**
     * Retrieve the limit instance
     *
     * @return \sndsgd\rate\LimitInterface
     */
    public function getLimit(): \sndsgd\rate\LimitInterface
    {
        return $this->limit;
    }

    /**
     * Determine whether the limit has been exceeded
     *
     * @return bool
     */
    public function isLimitExceeded(): bool
    {
        return ($this->hits > $this->limit->getLimit());
    }

    /**
     * Get the number of hits remaining
     *
     * @return int
     */
    public function getRemainingHits(): int
    {
        return max($this->limit->getLimit() - $this->hits, 0);
    }

    /**
     * Get the number seconds remaining
     *
     * @return int
     */
    public function getRemainingSeconds(): int
    {
        if ($this->timeout === -1) {
            return $this->limit->getDuration();
        }
        return max($this->timeout, 1);
    }

    /**
     * Get a header suitable for an HTTP response
     *
     * @param string $template A template for the comlete header
     * @return string
     */
    public function getHeader(
        $template = "X-RateLimit-%s: Limit: %d, Hits-Remaining: %d, Reset-In: %d"
    ): string
    {
        if ($this->limit->isHidden()) {
            return "";
        }

        $name = preg_replace("/[^a-zA-Z0-9-]/", "-", $this->limit->getName());
        return sprintf(
            $template,
            $name,
            $this->limit->getLimit(),
            $this->getRemainingHits(),
            $this->getRemainingSeconds()
        );
    }
}
