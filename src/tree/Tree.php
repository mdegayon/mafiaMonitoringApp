<?php

namespace Src\tree;

use Src\tree\mafia\MafiaNode;

class Tree
{
    public Node $root;
    //TODO Think about ops with root (deleting root, moving root,...)

    public function __construct($rootData)
    {
        $this->root = new Node($rootData);
    }

    public function getRoot() : Node
    {
        return $this->root;
    }

    public function setRoot($rootData): Node
    {
        $this->root = new Node($rootData);
    }

    public function add($data, Node $parent): Node
    {
        $node = new Node($data);
        $node->setParent($parent);
        $parent->addChild($node);

        return $node;
    }

    public function remove($data, Node $parent): bool
    {
        return $parent->removeChild($data);
    }

    public function removeNode(Node $node, Node $parent): bool
    {
        return $parent->removeChildNode($node);
    }

    public function moveNode(Node $node, Node $newParent): bool
    {
        //TODO Change behavior so even root node can be replaced
        if ($node->equals($this->root)){
            throw new \DomainException(sprintf("Can't move root node (node=%s)",$node ));
        }

        $previousParent = $node->getParent();
        if ($previousParent !== Node::EMPTY_NODE) {
            $this->removeNode($node, $previousParent);
        }

        $newParent->addChild($node);

        return true;
    }

    public function traverseFromRoot(): void
    {
        $this->traverse($this->root, 0);
    }

    public function traverse(Node $node, $level = 0): void
    {
        if ($node !== null) {
            echo str_repeat("-", $level) . $node . PHP_EOL;
            foreach ($node->getChildren() as $child) {
                $this->traverse($child, $level + 1);
            }
        }
    }

}