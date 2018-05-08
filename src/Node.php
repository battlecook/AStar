<?php

namespace battlecook\AStar;

final class Node
{
    private $x;
    private $y;

    private $index;
    private $f;

    private $g;
    private $heuristic;

    private $parent;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->index = $this->x . '_' . $this->y;
        $this->g = 0;
        $this->heuristic = 0;
        $this->parent = null;
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

    public function setHeuristic(int $heuristic)
    {
        $this->heuristic = $heuristic;
    }

    public function getF(): int
    {
        return $this->g + $this->heuristic;
    }

    public function setF(int $f)
    {
        $this->f = $f;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }
}