<?php

declare(strict_types = 1);

namespace battlecook\AStar;

final class AStar
{
    private const DIRECT_WEIGHT = 10;
    private const OBLIQUE_WEIGHT = 14;

    private $route;

    private $start;
    private $end;

    private $xRange;
    private $yRange;

    private $open;
    private $close;

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

        if($this->inRange($start) === false)
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

        $this->open = new NodeCollection();
        $this->close = new NodeCollection();
    }

    /**
     * @return Node[]
     */
    public function route(): array
    {
        $current = new Node($this->start->getX(), $this->start->getY());
        $current->setHeuristic($this->getHeuristic($current->getX(), $current->getY()));

        $this->open->add($current);

        while($this->open->count() > 0)
        {
            $current = $this->open->getMinFNode();
            $this->open->remove($current);
            $this->close->add($current);

            if ($this->isEndPoint($current))
            {
                while ($parent = $current->getParent())
                {
                    if($parent === null)
                    {
                        break;
                    }
                    $tmp = $this->close->get($current);
                    $this->route[] = new Point($tmp->getX(), $tmp->getY());
                    $current = $this->close->get($parent);
                }
                $this->route[] = new Point($this->start->getX(), $this->start->getY());

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
            new Node($current->getX() + 1, $current->getY()),
            new Node($current->getX(), $current->getY() + 1),
            new Node($current->getX() - 1, $current->getY()),
            new Node($current->getX(), $current->getY() - 1)
        );

        foreach($directDirectionGroup as $key => $directDirection)
        {
            if(in_array(new Point($directDirection->getX(), $directDirection->getY()), $this->obstacleList))
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
            $aroundNode = new Node($current->getX() + $obliqueDirection->getX(), $current->getY() + $obliqueDirection->getY());
            if(in_array(new Point($aroundNode->getX(), $aroundNode->getY()), $this->obstacleList))
            {
                continue;
            }
            if($this->close->get($aroundNode) === null && $this->inRange(new Point($aroundNode->getX(), $aroundNode->getY())))
            {
                if($this->open->get($aroundNode))
                {
                    if($this->open->get($aroundNode)->getG() > $this->getG($current, self::OBLIQUE_WEIGHT))
                    {
                        $this->open->get($aroundNode)->setParent($current);
                    }
                }
                else
                {
                    $aroundNode->setG($this->getG($current, self::OBLIQUE_WEIGHT));
                    $aroundNode->setHeuristic($this->getHeuristic($aroundNode->getX(), $aroundNode->getY()));
                    $aroundNode->setParent($current);

                    $this->open->add($aroundNode);
                }
            }
        }
    }

    private function addNodeInOpen(Node $aroundNode, Node $current)
    {
        if($this->close->get($aroundNode) === null && $this->inRange(new Point($aroundNode->getX(), $aroundNode->getY())))
        {
            if($this->open->get($aroundNode))
            {
                if($this->open->get($aroundNode)->getG() > $this->getG($current, self::DIRECT_WEIGHT))
                {
                    $this->open->get($aroundNode)->setParent($current);
                }
            }
            else
            {
                $aroundNode->setG($this->getG($current, self::DIRECT_WEIGHT));
                $aroundNode->setHeuristic($this->getHeuristic($aroundNode->getX(), $aroundNode->getY()));
                $aroundNode->setParent($current);

                $this->open->add($aroundNode);
            }
        }
    }

    private function isEndPoint(Node $node): bool
    {
        return $node->getX() === $this->end->getX() && $node->getY() === $this->end->getY();
    }

    private function getG(Node $node, $weight): int
    {
        return $node->getG() + $weight;
    }

    private function getHeuristic(int $x, int $y)
    {
        $yLength = abs($y - $this->end->getY());
        $xLength = abs($x - $this->end->getX());

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