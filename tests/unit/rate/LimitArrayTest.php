<?php

namespace sndsgd\rate;

/**
 * @coversDefaultClass \sndsgd\rate\LimitArray
 */
class LimitArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        new LimitArray();
        new LimitArray(new Limit("one", "one"));
        new LimitArray(new Limit("one", "one"), new Limit("two", "two"));
    }
}
