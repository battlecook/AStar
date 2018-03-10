<?php

namespace battlecook\AStar;

final class Range
{
    private $min;
    private $max;

    /**
     * Range constructor.
     *
     * @param $min
     * @param $max
     * @throws AStarException
     */
    public function __construct(int $min, int $max)
    {
        if($min >= $max)
        {
            throw new AStarException("min greater than or equal to max");
        }

        $this->min = $min;
        $this->max = $max;
    }

    public function inRange(int $value): bool
    {
        return $value >= $this->min && $value <= $this->max;
    }

    public function getMin()
    {
        return $this->min;
    }

    public function getMax()
    {
        return $this->max;
    }
}