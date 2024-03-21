<?php

namespace Src\tree;

class Node
{
    protected mixed $data;
    protected array $children;
    protected Node|null $parent = self::EMPTY_NODE;

    const MISSING_ID = -1;
    const EMPTY_NODE = null;

    public function __construct($data, ?Node $parent = Node::EMPTY_NODE)
    {
        $this->data = $data;
        $this->children = [];
        $this->parent = $parent;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getParent(): Node|null
    {
        return $this->parent;
    }

    public function hasParent(): bool
    {
        return $this->parent !== Node::EMPTY_NODE;
    }

    public function setParent(?Node $parent): void
    {
        $this->parent = $parent;
    }

    public function addChildNode(Node $node): void
    {
        $this->children[] = $node;
        $node->setParent($this);
    }

    public function removeChild($data): bool
    {
        $targetNodeIndex = $this->findIndex($data);
        if ($targetNodeIndex === -1) {
            return false;
        }

        $targetNode = $this->children[$targetNodeIndex];
        $targetNode->setParent(Node::EMPTY_NODE);
        unset($this->children[$targetNodeIndex]);

        return true;
    }

    public function removeChildNode(Node $node): bool
    {
        return $this->removeChild($node->data);
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function findNode(Node $node): ?Node
    {
        return $this->find($node->data);
    }

    public function find($data): ?Node
    {
        $foundNode = Node::EMPTY_NODE;
        $nodeIndex = $this->findIndex($data);
        if ($nodeIndex != -1) {
            $foundNode = $this->children[$nodeIndex];
        }
        return $foundNode;
    }

    private function findIndex($data): int
    {
        $targetIndex = -1;
        foreach ($this->children as $childIndex => $child) {
            if ($child->data == $data) {
                $targetIndex = $childIndex;
                break;
            }
        }
        return $targetIndex;
    }

    public function equals(Node $node): bool
    {
        return $this->data == $node->data;
    }

    public function __toString(): string
    {
        return strval($this->data);
    }

}