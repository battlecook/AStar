<?php

namespace battlecook\AStar;

final class NodeCollection
{
   private $nodeMap;
   private $minNode;

   public function __construct()
   {
       $this->nodeMap = array();
       $this->minNode = null;
   }

   public function get(Node $node): ?Node
   {
       if(isset($this->nodeMap[$node->getIndex()]))
       {
           return $this->nodeMap[$node->getIndex()];
       }
       return null;
   }

   public function add(Node $node)
   {
       if(isset($this->nodeMap[$node->getIndex()]) === false)
       {
           $this->nodeMap[$node->getIndex()] = $node;
           if($this->minNode === null || ($this->minNode instanceof Node && $this->minNode->getF() > $node->getF()))
           {
               $this->minNode = $node;
           }
       }
   }

   public function remove(Node $node)
   {
       unset($this->nodeMap[$node->getIndex()]);
   }

   public function count()
   {
       return count($this->nodeMap);
   }

   public function getMinFNode(): ?Node
   {
       $f = null;
       $min = null;
       foreach ($this->nodeMap as $node)
       {
           if ($f === null || $f > $node->getF())
           {
               $min = $node;
               $f = $node->getF();
           }
       }

       return $min;
   }
}