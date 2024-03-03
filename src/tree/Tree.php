<?php

namespace Src\tree;

class Tree
{
    public Node $root;

    public function __construct(Node $root) {
        $this->root = $root;
    }

    public function traverseFromRoot() : void {
        $this->traverse($this->root, 0);
    }

    public function traverse(Node $node, $level = 0) : void {
        if ($node !== null) {
            echo str_repeat("-", $level) . $node . PHP_EOL;
            foreach ($node->getChildren() as $child) {
                $this->traverse($child, $level + 1);
            }
        }
    }

}