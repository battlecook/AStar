<?php

require __DIR__  . '/../vendor/autoload.php';

use battlecook\AStar\AStar;
use battlecook\AStar\Point;
use battlecook\AStar\Range;
use PHPUnit\Framework\TestCase;

class AStarTest extends TestCase
{
    /**
     * @throws \battlecook\AStar\AStarException
     */
    public function testCreate()
    {
        //given
        $start = new Point(3, -5);
        $end = new Point(3, -3);

        $xRange = new Range(-4, 4);
        $yRange = new Range(-6, -1);

        $obstacleList = array();
        $obstacleList[] = new Point(5,2);
        $obstacleList[] = new Point(5,3);
        $obstacleList[] = new Point(5,4);

        //when
        $aStar = new AStar($start, $end, $xRange, $yRange, $obstacleList);

        //then
        $this->assertInstanceOf('battlecook\AStar\AStar', $aStar);
    }

    /**
     * @expectedException \battlecook\AStar\AStarException
     * @expectedExceptionMessage  start point is out of range
     */
    public function testStartPointOutOfRange()
    {
        //given
        $start = new Point(-5, -6);
        $end = new Point(4, 0);

        $xRange = new Range(-4, 4);
        $yRange = new Range(-6, -1);

        $obstacleList = array();

        //when
        new AStar($start, $end, $xRange, $yRange, $obstacleList);

        //then
    }

    /**
     * @expectedException \battlecook\AStar\AStarException
     * @expectedExceptionMessage  end point is out of range
     */
    public function testEndPointOutOfRange()
    {
        //given
        $start = new Point(-4, -6);
        $end = new Point(4, 0);

        $xRange = new Range(-4, 4);
        $yRange = new Range(-6, -1);

        $obstacleList = array();

        //when
        new AStar($start, $end, $xRange, $yRange, $obstacleList);

        //then
    }

    /**
     * @expectedException \battlecook\AStar\AStarException
     * @expectedExceptionMessage  obstacle group have invalid type
     */
    public function testObstacleGroupHaveInvalidType()
    {
        //given
        $start = new Point(-2, 0);
        $end = new Point(2, 0);

        $xRange = new Range(-4, 3);
        $yRange = new Range(-3, 2);

        $obstacleList = array();
        $obstacleList[] = new Point(0,0);
        $obstacleList[] = array(0,1);
        $obstacleList[] = new Point(0,-1);

        //when
        new AStar($start, $end, $xRange, $yRange, $obstacleList);

        //then
    }

    /**
     * @throws \battlecook\AStar\AStarException
     */
    public function testRoute()
    {
        //given
        $start = new Point(-2, 0);
        $end = new Point(2, 0);

        $xRange = new Range(-4, 3);
        $yRange = new Range(-3, 2);

        $obstacleList = array();
        $obstacleList[] = new Point(0,0);
        $obstacleList[] = new Point(0,1);
        $obstacleList[] = new Point(0,-1);

        $aStar = new AStar($start, $end, $xRange, $yRange, $obstacleList);

        //when
        $route = $aStar->route();

        //then
        $expectedRoute = array(
            new Point(-2, 0),
            new Point(-1, 1),
            new Point(-1, 2),
            new Point(0, 2),
            new Point(1, 2),
            new Point(2, 1),
            new Point(2, 0),
        );
        $this->assertEquals($expectedRoute, $route);
    }
}
