<?php

require __DIR__  . '/../vendor/autoload.php';

use battlecook\AStar\Range;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    /**
     * @throws \battlecook\AStar\AStarException
     */
    public function testCreate()
    {
        //given

        //when
        $range = new Range(-4, 4);

        //then
        $this->assertInstanceOf('battlecook\AStar\Range', $range);
    }

    /**
     * @expectedException \battlecook\AStar\AStarException
     * @expectedExceptionMessage min greater than or equal to max
     */
    public function testInvalidParameter()
    {
        //given

        //when
        $range = new Range(4, -4);

        //then
    }

    /**
     * @throws \battlecook\AStar\AStarException
     */
    public function testInRange()
    {
        //given

        //when
        $range = new Range(-4, 4);
        $ret = $range->inRange(3);

        //then
        $this->assertTrue($ret);
    }

    /**
     * @throws \battlecook\AStar\AStarException
     */
    public function testNotInRange()
    {
        //given

        //when
        $range = new Range(-4, 4);
        $ret = $range->inRange(-5);

        //then
        $this->assertFalse($ret);
    }
}
