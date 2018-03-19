<?php

namespace battlecook\AStar;

final class Node
{
    public $x;
    public $y;

    public $index;
    public $f;

    public $g;
    public $heuristic;

    private $parent;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->index = $this->x . '_' . $this->y;
        $this->g = 0;
        $this->parent = null;
    }

    public function update()
    {
        $this->f = $this->g + $this->heuristic;
    }

    public function setParent(Node $parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }
}