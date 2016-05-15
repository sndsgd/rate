<?php

namespace sndsgd\rate;

/**
 * A read only typed array consisting of instances of sndsgd\rate\Limit
 */
class LimitArray extends \sndsgd\ArrayAbstract
{
    /**
     * @param \sndsgd\rate\Limit ...$limits
     */
    public function __construct(Limit ...$limits)
    {
        parent::__construct($limits, true);
    }
}
