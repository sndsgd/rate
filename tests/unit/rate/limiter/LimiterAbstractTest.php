<?php

namespace sndsgd\rate\limiter;

class LimiterAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerConstructor
     */
    public function testContructor($limits, $exception)
    {
        if ($exception) {
            $this->setExpectedException($exception);
        }

        $mock = $this->getMockBuilder(LimiterAbstract::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $rc = new \ReflectionClass($mock);
        $constructor = $rc->getConstructor();
        $constructor->invoke($mock, $limits);
    }

    public function providerConstructor()
    {
        $limit = new \sndsgd\rate\Limit("test", "info");
        return [
            [[], \InvalidArgumentException::class],
            [[1, 2, 3], \InvalidArgumentException::class],
            [[$limit], ""],
        ];
    }

    private function getLimiterMockWithPeriods(array $periods)
    {
        $mock = $this->getMockBuilder(LimiterAbstract::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $rc = new \ReflectionClass($mock);
        $property = $rc->getProperty("periods");
        $property->setAccessible(true);
        $property->setValue($mock, $periods);

        return $mock;
    }

    /**
     * @dataProvider providerIsExceeded
     */
    public function testIsExceeded(array $periods, $expect)
    {
        $mock = $this->getLimiterMockWithPeriods($periods);
        $this->assertSame($expect, $mock->isExceeded());
    }

    public function providerIsExceeded()
    {
        $makePeriod = function($isLimitExceeded) {
            $mock = $this->getMockBuilder(\sndsgd\rate\limit\Period::class)
                ->disableOriginalConstructor()
                ->setMethods(["isLimitExceeded"])
                ->getMock();
            $mock->method("isLimitExceeded")->willReturn($isLimitExceeded);
            return $mock;
        };

        return [
            [[], false],
            [[$makePeriod(false)], false],
            [[$makePeriod(true)], true],
            [[$makePeriod(false), $makePeriod(true)], true],
        ];
    }

    /**
     * @dataProvider providerGetHeaders
     */
    public function testGetHeaders(array $periods, $expect)
    {
        $mock = $this->getLimiterMockWithPeriods($periods);
        $this->assertSame($expect, $mock->getHeaders());
    }

    public function providerGetHeaders()
    {
        $makePeriod = function($header) {
            $mock = $this->getMockBuilder(\sndsgd\rate\limit\Period::class)
                ->disableOriginalConstructor()
                ->setMethods(["getHeader"])
                ->getMock();
            $mock->method("getHeader")->willReturn($header);
            return $mock;
        };

        return [
            [[$makePeriod("a")], ["a"]],
            [[$makePeriod("a"), $makePeriod("b")], ["a", "b"]],
        ];
    }

    public function testGetPeriods()
    {
        $mock = $this->getMockBuilder(LimiterAbstract::class)
            ->disableOriginalConstructor()
            ->setMethods(["increment"])
            ->getMockForAbstractClass();

        $mock->expects($this->once())->method("increment");
        $mock->getPeriods();
    }
}
