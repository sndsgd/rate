<?php

namespace sndsgd\rate;

class LimitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerConstructor
     */
    public function testConstructor($name, $info, $limit, $duration, $exception)
    {
        if ($exception) {
            $this->setExpectedException($exception);
        }

        $test = new Limit($name, $info, $limit, $duration);
    }

    public function providerConstructor()
    {
        return [
            ["name", "info", 0, 1, \InvalidArgumentException::class],
            ["name", "info", 1, 0, \InvalidArgumentException::class],
            ["name", "info", 1, 1, ""],
        ];
    }

    /**
     * @dataProvider providerGetHeaderKey
     */
    public function testGetHeaderKey($name, $info, $expect)
    {
        $test = new Limit($name, $info);
        $this->assertSame($expect, $test->getHeaderKey());
    }

    public function providerGetHeaderKey()
    {
        return [
            ["name", "info", "X-RateLimit-name"],
            ["Name", "info", "X-RateLimit-Name"],
        ];
    }

    /**
     * @dataProvider providerGetHeaderValue
     */
    public function testGetHeaderValue($limit, $hits, $ttl, $expect)
    {
        $test = new Limit("name", "info", $limit);
        $this->assertSame($expect, $test->getHeaderValue($hits, $ttl));
    }

    public function providerGetHeaderValue()
    {
        $template = "Limit: %d; Remaining-Hits: %d; Reset-In: %d";
        return [
            [3, 2, 5, sprintf($template, 3, 1, 5)],
        ];
    }

    /**
     * @dataProvider providerGetCacheKey
     */
    public function testGetCacheKey($n, $i, $l, $d, $expect)
    {
        $test = new Limit($n, $i, $l, $d);
        $this->assertSame($expect, $test->getCacheKey());
    }

    public function providerGetCacheKey()
    {
        return [
            ["a", "b", 1, 2, "a|b|1|2"],
        ];
    }

    /**
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
