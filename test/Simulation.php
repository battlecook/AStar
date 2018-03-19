<?php

use battlecook\AStar\AStar;
use battlecook\AStar\Point;
use battlecook\AStar\Range;

require __DIR__  . '/../vendor/autoload.php';

$minX = rand(-10, -1);
$maxX = rand(0, 10);

$minY = rand(-10, -1);
$maxY = rand(0, 10);

$xRange = new Range($minX, $maxX);
$yRange = new Range($minY, $maxY);

$startX = rand($minX, $maxX);
$startY = rand($minY, $maxY);

$start = new Point($startX, $startY);

$endX = rand($minX, $maxX);
$endY = rand($minY, $maxY);

$end = new Point($endX, $endY);

$obstacleCount = (int) (($maxX - $minX) * ($maxY - $minY) * 0.3);

$obstacleList = array();
for($i=0; $i<$obstacleCount; $i++)
{
    $obstacle = new Point(rand($minX, $maxX),rand($minY, $maxY));
    if($obstacle->getX() === $start->getX() && $obstacle->getY() === $start->getY()
    || $obstacle->getX() === $end->getX() && $obstacle->getY() === $end->getY()
    )
    {
        continue;
    }
    $obstacleList[] = $obstacle;
}

$aStar = new AStar($start, $end, $xRange, $yRange, $obstacleList);
$aStar->route();
$aStar->displayRoute();