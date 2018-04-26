<?php

namespace battlecook\AStar;

final class Node
{
    public $x;
    public $y;

    private $index;
    public $f;

    private $g;
    private $heuristic;

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

    public function getParent(): ?Node
    {
        return $this->parent;
    }

    public function getG(): int
    {
        return $this->g;
    }

    public function setG(int $g)
    {
        $this->g = $g;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getHeuristic()
    {
        return $this->heuristic;
    }

    public function setHeuristic($heuristic)
    {
        $this->heuristic = $heuristic;
    }
}