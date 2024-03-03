<?php

namespace Src\tree\mafia;

use Src\tree\Node;

class MafiaNode extends Node
{
    private MafiaNode|null $replacementNode;
    private MafiaNode|null $originalBoss;

    public function getOriginalBoss(): ?MafiaNode
    {
        return $this->originalBoss;
    }

    public function setOriginalBoss(MafiaNode $originalBoss): void
    {
        $this->originalBoss = $originalBoss;
    }

    public function getDirectSubordinates() : array
    {
        return $this->getChildren();
    }

    public function getSubordinates(): array
    {
        $subordinates = $this->getChildren();
        foreach ($this->getChildren() as $child){
            $subordinates[] = $child->getChildren();
        }

        return $subordinates;
    }

    public function getReplacementNode(): ?MafiaNode
    {
        return $this->replacementNode;
    }

    public function setReplacementNode(MafiaNode $replacementNode): void
    {
        $this->replacementNode = $replacementNode;
    }

    public function hasBoss() : bool
    {
        return $this->getParent() !== self::EMPTY_NODE;
    }

    public function removeFromOrganization() : void
    {
        if ($this->hasDirectSubordinates()){
            throw new \DomainException("Can't remove Node with Subordinates. Node: $this");
        }
        if (!$this->hasBoss()){
            throw new \DomainException("Can't remove Node with no Boss from organization. He might be The Don. Node: $this");
        }

        $boss = $this->getParent();
        $boss->removeChild($this);
    }

    private function hasDirectSubordinates() : bool
    {
        return !empty($this->getDirectSubordinates());
    }

    public function removeChild(MafiaNode $child) : void
    {
        $index = $this->findChildIndex($child);
        if ($index === false){
            throw new \InvalidArgumentException("Can't find node in children list. Node: $child");
        }

        $child->setParent(Node::EMPTY_NODE);
        $this->removeChildAt($index);
    }

    private function removeChildAt(int $index) : void
    {
        if ( !array_key_exists($index, $this->children)){
            throw new InvalidArgumentException(
                sprintf(
                    "Can't remove node in children list with given index. Index: %d",
                    $index
                )
            );
        }
        unset($this->children[$index]);
    }

    private function findChildIndex(MafiaNode $needleNode) : false|int
    {
        return array_search($needleNode, $this->getDirectSubordinates());
    }

    public function getRank() : int
    {
        return $this->getParent() ? $this->getParent()->getRank() + 1 : 1;
    }

    public function hasNSubordinatesUnder(int $subordinatesNumber) : bool
    {
        return $this->countSubordinatesWithThreshold($subordinatesNumber, 0) >= $subordinatesNumber;
    }

    private function countSubordinatesWithThreshold(int $threshold, int $count) : int
    {
        $subordinatesCount = $count;
        if ($subordinatesCount >= $threshold){
            return $count;
        }

        foreach ($this->getChildren() as $child){
            $subordinatesCount = $child->countSubordinatesWithThreshold($threshold, ++$subordinatesCount);
            if ($subordinatesCount >= $threshold){
                break;
            }
        }

        return $subordinatesCount;
    }

}