<?php

namespace sndsgd\rate;

/**
 * @coversDefaultClass \sndsgd\rate\Limit
 */
class LimitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getHash
     * @dataProvider providerGetHash
     */
    public function testGetHash($n, $i, $l, $d, $expect)
    {
        $test = new Limit($n, $i, $l, $d);
        $this->assertSame($expect, $test->getHash());
    }

    public function providerGetHash()
    {
        return [
            ["a", "b", 1, 2, "a|b|1|2"],
        ];
    }

    /**
     * @covers ::toArray
     * @covers ::jsonSerialize
     * @dataProvider providerToArray
     */
    public function testToArray($name, $limit, $duration, $expect)
    {
        $test = new Limit($name, "info", $limit, $duration);
        $result = $test->toArray();
        $this->assertSame($expect, $result);
        $this->assertSame(json_encode($result), json_encode($test));
    }

    public function providerToArray()
    {
        return [
            ["a", 1, 2, ["name" => "a", "limit" => 1, "duration" => 2]],
        ];
    }
}
