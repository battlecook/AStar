<?php

require __DIR__  . '/../vendor/autoload.php';

use battlecook\AStar\NodeCollection;
use PHPUnit\Framework\TestCase;

class NodeCollectionTest extends TestCase
{
    public function testCreate()
    {
        //given

        //when
        $nodeCollection = new NodeCollection();

        //then
        $this->assertInstanceOf('battlecook\AStar\NodeCollection', $nodeCollection);
    }

    public function testCount()
    {
        //given
        $nodeCollection = new NodeCollection();

        $node1 = new \battlecook\AStar\Node(1,1);
        $node2 = new \battlecook\AStar\Node(2,2);

        //when
        $nodeCollection->add($node1);
        $nodeCollection->add($node2);

        //then
        $this->assertEquals(2, $nodeCollection->count());
    }
}
