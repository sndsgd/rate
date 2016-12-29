<?php

namespace sndsgd\rate\limit;

class PeriodTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLimit()
    {
        $limit = new \sndsgd\rate\Limit('', '');
        $period = new Period($limit);
        $this->assertSame($limit, $period->getLimit());
    }

    /**
     * @dataProvider providerIsLimitExceeded
     */
    public function testIsLimitExceeded($limit, $hits, $expect)
    {
        $limit = new \sndsgd\rate\Limit('', '', $limit);
        $period = new Period($limit, $hits);
        $this->assertSame($expect, $period->isLimitExceeded());
    }

    public function providerIsLimitExceeded()
    {
        return [
            [2, 3, true],
            [2, 2, false],
            [2, 1, false],
        ];
    }

    /**
     * @dataProvider providerGetRemainingHits
     */
    public function testGetRemainingHits($limit, $hits, $expect)
    {
        $limit = new \sndsgd\rate\Limit('', '', $limit);
        $period = new Period($limit, $hits);
        $this->assertSame($expect, $period->getRemainingHits());
    }

    public function providerGetRemainingHits()
    {
        return [
            [10, 0, 10],
            [10, 1, 9],
            [10, 9, 1],
            [10, 10, 0],
            [10, 11, 0],
        ];
    }

    /**
     * @dataProvider providerGetRemainingSeconds
     */
    public function testGetRemainingSeconds($duration, $timeout, $expect)
    {
        $limit = new \sndsgd\rate\Limit('', '', 1, $duration);
        $period = new Period($limit, 1, $timeout);
        $this->assertSame($expect, $period->getRemainingSeconds());
    }

    public function providerGetRemainingSeconds()
    {
        return [
            [10, -1, 10],
            [10, 0, 1],
            [10, 1, 1],
            [10, 9, 9],
        ];
    }

    /**
     * @dataProvider providerGetHeader
     */
    public function testGetHeader(
        $name,
        $limit,
        $hidden,
        $remainingHits,
        $remainingSeconds,
        $expect
    )
    {
        $limit = new \sndsgd\rate\Limit($name, '', $limit, 60, $hidden);
        $mock = $this->getMockBuilder(Period::class)
            ->setConstructorArgs([$limit])
            ->setMethods(["getRemainingHits", "getRemainingSeconds"])
            ->getMock();

        $mock->method("getRemainingHits")->willReturn($remainingHits);
        $mock->method("getRemainingSeconds")->willReturn($remainingSeconds);

        $this->assertSame($expect, $mock->getHeader());
    }

    public function providerGetHeader()
    {
        return [
            [
                "Hidden Test",
                1,
                true,
                5,
                5,
                ""
            ],
            [
                "Test",
                10,
                false,
                5,
                5,
                "X-RateLimit-Test: Limit: 10, Hits-Remaining: 5, Reset-In: 5"
            ],
            [
                "Te!st",
                10,
                false,
                5,
                5,
                "X-RateLimit-Te-st: Limit: 10, Hits-Remaining: 5, Reset-In: 5"
            ],
        ];
    }
}
