<?php

namespace battlecook\AStar;

final class AStar
{
    const DIRECT_WEIGHT = 10;
    const OBLIQUE_WEIGHT = 14;

    private $route;

    private $start;
    private $end;

    private $xRange;
    private $yRange;

    public $open;
    public $close;

    public $obstacleList = array();

    /**
     * Astar constructor.
     * @param Point $start
     * @param Point $end
     * @param Range $xRange
     * @param Range $yRange
     * @param Point[] $obstacleGroup
     * @throws AStarException
     */
    public function __construct(Point $start, Point $end, Range $xRange, Range $yRange, array $obstacleGroup)
    {
        $this->xRange = $xRange;
        $this->yRange = $yRange;

        if($this->inRange($start) ===false)
        {
            throw new AStarException("start point is out of range");
        }

        if($this->inRange($end) === false)
        {
            throw new AStarException("end point is out of range");
        }

        foreach($obstacleGroup as $obstacle)
        {
            if(($obstacle instanceof Point) === false)
            {
                throw new AStarException("obstacle group have invalid type");
            }
        }

        $this->start = $start;
        $this->end = $end;

        $this->obstacleList = $obstacleGroup;
        $this->route = array();
    }

    /**
     * @return Node[]
     */
    public function route(): array
    {
        $current = new Node($this->start->getX(), $this->start->getY());
        $current->f = $current->heuristic = $this->getHeuristic($current);

        $this->open[$current->index] = $current;

        while(count($this->open) > 0)
        {
            $current = $this->getMinFNode();
            unset($this->open[$current->index]);
            $this->close[$current->index] = $current;

            if ($this->isEndPoint($current))
            {
                $this->route[] = new Point($this->end->getX(), $this->end->getY());
                while ($current->getParent() !== null)
                {
                    $tmp = $this->close[$current->getParent()->index];
                    $this->route[] = new Point($tmp->x, $tmp->y);
                    $current = $this->close[$current->getParent()->index];
                }

                $this->route = array_reverse($this->route);
            }

            $this->updateNeighborNode($current);
        }
        return $this->route;
    }

    private function inRange(Point $point): bool
    {
        if($this->xRange->inRange($point->getX()) && $this->yRange->inRange($point->getY()))
        {
            return true;
        }

        return false;
    }

    private function getMinFNode(): Node
    {
        $f = null;
        $min = null;
        foreach ($this->open as $index => $node)
        {
            if ($f === null || $f > $node->f)
            {
                $min = $index;
                $f = $node->f;
            }
        }

        return $this->open[$min];
    }

    /**
     * @param Node $current
     */
    private function updateNeighborNode(Node $current)
    {
        $obliqueDirectionGroup = array(
            new Point(1, 1),
            new Point(-1, 1),
            new Point(-1, -1),
            new Point(1, -1),
        );

        $directDirectionGroup = array(
            new Node($current->x + 1, $current->y),
            new Node($current->x, $current->y + 1),
            new Node($current->x - 1, $current->y),
            new Node($current->x, $current->y - 1)
        );

        foreach($directDirectionGroup as $key => $directDirection)
        {
            if(in_array(new Point($directDirection->x, $directDirection->y), $this->obstacleList))
            {
                unset($obliqueDirectionGroup[$key]);
                if($key - 1 < 0)
                {
                    unset($obliqueDirectionGroup[3]);
                }
                else
                {
                    unset($obliqueDirectionGroup[$key - 1]);
                }
            }
            else
            {
                $this->addNodeInOpen($directDirection, $current);
            }
        }

        foreach($obliqueDirectionGroup as $obliqueDirection)
        {
            $aroundNode = new Node($current->x + $obliqueDirection->getX(), $current->y + $obliqueDirection->getY());
            if(in_array(new Point($aroundNode->x, $aroundNode->y), $this->obstacleList))
            {
                continue;
            }
            if(!isset($this->close[$aroundNode->index]) && $this->inRange(new Point($aroundNode->x, $aroundNode->y)))
            {
                if(isset($this->open[$aroundNode->index]))
                {
                    if($this->open[$aroundNode->index]->getG() > $this->getG($current, self::OBLIQUE_WEIGHT))
                    {
                        $this->open[$aroundNode->index]->setParent($current);
                    }
                }
                else
                {
                    $aroundNode->setG($this->getG($current, self::OBLIQUE_WEIGHT));
                    $aroundNode->heuristic = $this->getHeuristic($aroundNode);
                    $aroundNode->update();
                    $aroundNode->setParent($current);
                    $this->open[$aroundNode->index] = $aroundNode;
                }
            }
        }
    }

    private function addNodeInOpen(Node $aroundNode, Node $current)
    {
        if(!isset($this->close[$aroundNode->index]) && $this->inRange(new Point($aroundNode->x, $aroundNode->y)))
        {
            if(isset($this->open[$aroundNode->index]))
            {
                if($this->open[$aroundNode->index]->getG() > $this->getG($current, self::DIRECT_WEIGHT))
                {
                    $this->open[$aroundNode->index]->setParent($current);
                }
            }
            else
            {
                $aroundNode->setG($this->getG($current, self::DIRECT_WEIGHT));
                $aroundNode->heuristic = $this->getHeuristic($aroundNode);
                $aroundNode->update();
                $aroundNode->setParent($current);
                $this->open[$aroundNode->index] = $aroundNode;
            }
        }
    }

    private function isEndPoint(Node $node): bool
    {
        return $node->x == $this->end->getX() && $node->y == $this->end->getY();
    }

    private function getG(Node $node, $weight): int
    {
        return $node->getG() + $weight;
    }

    private function getHeuristic(Node $node): int
    {
        $yLength = abs($node->y - $this->end->getY());
        $xLength = abs($node->x - $this->end->getX());

        ($yLength < $xLength)? $obliqueCount = $yLength : $obliqueCount = $xLength;
        ($yLength < $xLength)? $directCount = $xLength - $obliqueCount : $directCount = $yLength - $obliqueCount;

        return $obliqueCount * self::OBLIQUE_WEIGHT + $directCount * self::DIRECT_WEIGHT;
    }

    public function displayRoute()
    {
        header('content-type:text/html;charset=utf-8');

        echo 'S : start point';
        echo '<br>';
        echo 'E : end point';
        echo '<br>';
        echo '<br>';
        echo 'Map size';
        echo '<br>';
        echo 'x : random ( -10 ~ 10 )';
        echo '<br>';
        echo 'y : random ( -10 ~ 10 )';
        echo '<br>';
        echo '<br>';

        echo '<table border="1">';
        for ($y = $this->yRange->getMin(); $y <= $this->yRange->getMax(); $y++)
        {
            echo '<tr>';
            for ($x = $this->xRange->getMin(); $x <= $this->xRange->getMax(); $x++)
            {
                $current = new Point($x, $y);
                if (in_array($current, $this->obstacleList))
                {
                    $bg = 'bgcolor="#000"';
                }
                elseif (in_array($current, $this->route))
                {
                    $bg = 'bgcolor="#5cb85c"';
                }
                else
                {
                    $bg = '';
                }

                if ($current->getX() === $this->start->getX() && $current->getY() === $this->start->getY())
                {
                    $content = 'S';
                }
                elseif ($current->getX() === $this->end->getX() && $current->getY() === $this->end->getY())
                {
                    $content = 'E';
                }
                else
                {
                    $content = '&nbsp;';
                }

                echo '<td style="width:22px; height: 22px;" ' . $bg . '>' . $content . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
}