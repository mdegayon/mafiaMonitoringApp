<?php declare(strict_types=1);

namespace Src\tree\mafia;

use Src\Mobster;
use Src\tree\Node;
use Src\tree\Tree;

class MafiaTree
{
    private Tree $mafiaTree;
    private array $nodesByMobsterKey;

    const NO_THRESHOLD = PHP_INT_MAX;

    public function __construct(Mobster $rootData)
    {
        $this->mafiaTree = new Tree($rootData);
        $this->addNodeWithKey($this->mafiaTree->getRoot(), $rootData->getKey());
    }

    private function addNodeWithKey(Node $node, string $key) : void
    {
        $this->nodesByMobsterKey[$key] = $node;
    }

    private function findNode(string $key) : Node {
        if ( !array_key_exists($key, $this->nodesByMobsterKey)){
            throw new \DomainException("Cant' find Node with key='$key'");
        }

        return $this->nodesByMobsterKey[$key];
    }

    public function getDon(): Mobster
    {
        return $this->mafiaTree->getRoot()->getData();
    }

    public function print(): void
    {
        $this->mafiaTree->traverseFromRoot();
    }

    public function contains(Mobster $mobster) : bool
    {
        return array_key_exists($mobster->getKey(), $this->nodesByMobsterKey);
    }

    public function countMobsters() : int
    {
        return count($this->nodesByMobsterKey);
    }

    public function getBossOfMobster(Mobster $mobster) : ?Mobster
    {
        $mobsterNode = $this->findNode($mobster->getKey());

        return $mobsterNode->hasParent() ? $mobsterNode->getParent()->getData() : Node::EMPTY_NODE;
    }

    public function addMobster(Mobster $mobster, Mobster $mobsterBoss) : void
    {
        $mobsterBossNode = $this->findNode($mobsterBoss->getKey());
        $mobsterNode = $this->mafiaTree->add($mobster, $mobsterBossNode);

        $this->addNodeWithKey($mobsterNode, $mobster->getKey());
    }

    public function removeMobster(Mobster $mobsterToRemove) : bool
    {
        $nodeRemoved = false; // TODO SYX Discuss naming conv
        $nodeToRemove = $this->findNode($mobsterToRemove->getKey());
        $parentNode =  $nodeToRemove->getParent();

        if($parentNode === Node::EMPTY_NODE){
            throw new \DomainException(
        "Can't remove node with no parent. It might likely be the Don. 
                mobsterToRemove=$mobsterToRemove. Don={$this->getDon()}"
            );
        }

        if ( $this->mafiaTree->remove($mobsterToRemove, $parentNode) ){
            $nodeRemoved =  $this->removeNode( $mobsterToRemove->getKey() );

            foreach ($nodeToRemove->getChildren() as $child){
                $nodeRemoved = $nodeRemoved && $this->removeMobster($child->getData());
            }
        }

        return $nodeRemoved;
    }

    private function removeNode(string $key) : bool
    {
        $nodeRemoved = false;
        if ($this->nodeExists($key)){
            unset($this->nodesByMobsterKey[$key]);
            $nodeRemoved = true;
        }
        return $nodeRemoved;
    }

    private function nodeExists($key) : bool
    {
        return array_key_exists($key, $this->nodesByMobsterKey);
    }

    public function getRank(Mobster $mobster) : int
    {
        $node = $this->findNode($mobster->getKey());

        return $this->getRankOfNode($node);
    }

    private function getRankOfNode(Node $node) : int
    {
        $rank = 1;
        if ( !$node->hasParent() ){
            return $rank;
        }
        $rank = 1 + $this->getRankOfNode($node->getParent());

        return $rank;
    }

    public function getDirectSubordinates(Mobster $mobster) : array
    {
        $directSubordinates = [];

        $mobsterNode = $this->findNode($mobster->getKey());
        foreach ($mobsterNode->getChildren() as $child){
            $directSubordinates[] = $child->getData();
        }

        return $directSubordinates;
    }

    public function getSubordinates(Mobster $mobster) : array
    {
        $mobsterNode = $this->findNode($mobster->getKey());
        return $this->getSubordinatesOfNode($mobsterNode);
    }

    private function getSubordinatesOfNode(Node $node) : array
    {
        $subordinates = [];
        $children = $node->getChildren();

        foreach ($children as $key => $child){
            $subordinates[] = $child->getData();
            $childSubordinates = $this->getSubordinatesOfNode($child);
            if (!empty($childSubordinates)){ // TODO SYX DISCUSS Get rid of nesting
                $subordinates = array_merge($subordinates, $childSubordinates);
            }
        }

        return $subordinates;
    }

    public function countSubordinatesWithThreshold(Mobster $mobster, int $threshold = self::NO_THRESHOLD) : int
    {
        if ($threshold <= 0){
            throw new \DomainException(
                "Threshold value should be an integer greater than zero. threshold={$threshold}"
            );
        }
        $mobsterNode = $this->findNode($mobster->getKey());
        return $this->countSubordinatesOfNodeWithThreshold($mobsterNode, $threshold);
    }

    private function countSubordinatesOfNodeWithThreshold(Node $node, int $threshold, int $count = 0) : int
    {
        $subordinatesCount = $count;
        if ( $subordinatesCount >= $threshold ){
            return $count;
        }

        foreach ($node->getChildren() as $child){
            $subordinatesCount = $this->countSubordinatesOfNodeWithThreshold($child, $threshold, ++$subordinatesCount);
            if ($subordinatesCount >= $threshold){
                break;
            }
        }

        return $subordinatesCount;
    }

}