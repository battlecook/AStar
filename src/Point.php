<?php

namespace battlecook\AStar;

final class Point
{
    public $x;
    public $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }
}