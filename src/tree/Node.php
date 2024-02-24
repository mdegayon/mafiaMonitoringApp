<?php

namespace Src\tree;

class Node
{
    public NodeData $data;
    public $children = [];
    public Node $parent;

    public function __construct(NodeData $data)
    {
        $this->data = $data;
    }

    public function getData() : NodeData
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getParent() : Node|null {
        return $this->parent;
    }

    public function setParent(Node $parent) : void{
        $this->parent = $parent;
    }

    public function addChild(Node $node) : void{
        $this->children[] = $node;
    }

    public function getChilds() : array{
        return $this->children;
    }

}