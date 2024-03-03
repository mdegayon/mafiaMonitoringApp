<?php

namespace Src\tree;

class Node
{
    protected mixed $data;
    protected $children = [];
    protected Node|null $parent = self::EMPTY_NODE;

    const MISSING_ID = -1;
    const EMPTY_NODE = null;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData() : mixed
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getParent() : Node|null
    {
        return $this->parent;
    }

    public function setParent(Node|null $parent) : void
    {
        $this->parent = $parent;
    }

    public function addChild(Node $node) : void
    {
        $this->children[] = $node;
        $node->setParent($this);
    }

    public function getChildren() : array
    {
        return $this->children;
    }

    public function __toString(): string
    {
        return $this->data->__toString();
    }

}