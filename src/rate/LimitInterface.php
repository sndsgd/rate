<?php

namespace sndsgd\rate;

interface LimitInterface
{
    /**
     * The client facing name for the limit
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Retrieve the number of hits allowed per quota period
     *
     * @return int
     */
    public function getLimit(): int;

    /**
     * Retrieve the number seconds per quota period
     *
     * @return int
     */
    public function getDuration(): int;

    /**
     * Whether the limit should be hidden from end users
     *
     * @return bool
     */
    public function isHidden(): bool;
}
