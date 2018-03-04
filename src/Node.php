<?php

namespace battlecook\AStar;

final class Node
{
    public $x;
    public $y;

    public $index;
    public $f;

    public $g; // 시작점부터 계산된 비용
    public $heuristic; // 도착점 까지 계산된 비용

    public $parent;

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
}