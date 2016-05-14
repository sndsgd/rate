<?php

namespace sndsgd\rate\limiter;

class RedisLimiterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerConstructor
     */
    public function testConstructor($redis, $limits)
    {
        $limiter = new RedisLimiter($redis, $limits);
    }

    public function providerConstructor()
    {
        return [
            [new \Redis(), [new \sndsgd\rate\Limit("test", "info")]],
        ];
    }

    /**
     * @dataProvider providerIncrement
     */
    public function testIncrement(
        array $limits,
        $incrementKeysResult,
        $expectSetTimeoutsCalled
    )
    {
        $redis = new \Redis();
        $mock = $this->getMockBuilder(RedisLimiter::class)
            ->setConstructorArgs([$redis, $limits])
            ->setMethods(["incrementKeys", "setTimeouts"])
            ->getMock();

        $mock
            ->expects($this->once())
            ->method("incrementKeys")
            ->willReturn($incrementKeysResult);

        $expects = $expectSetTimeoutsCalled ? $this->once() : $this->never();
        $mock->expects($expects)->method("setTimeouts");

        $result = $mock->increment();
    }

    public function providerIncrement()
    {
        $limit1 = new \sndsgd\rate\Limit("1", "a");
        $limit2 = new \sndsgd\rate\Limit("2", "b");

        return [
            [
                [$limit1],
                [5, 2],
                false,
            ],
            [
                [$limit1, $limit2],
                [5, 2, 1, -1],
                true,
            ],
        ];
    }
}
