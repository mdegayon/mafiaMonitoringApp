<?php declare(strict_types=1);

namespace Src\tree\mafia;

use Src\tree\Node;
use Src\tree\Tree;

class MafiaTree
{
    private Tree $mafiaTree;

    const   FIRST_RANKS_HIGHER = 1,
            SECOND_RANKS_HIGHER = -1,
            RANK_EQUAL = 0;

    public function __construct(Node $root)
    {
        $this->mafiaTree = new Tree($root);
    }

    public function getDon(): Node
    {
        return $this->mafiaTree->root;
    }

    public function print(): void
    {
        $this->mafiaTree->traverseFromRoot();
    }

    public function addNode(MafiaNode $mobster, MafiaNode $mobsterBoss) : void
    {
        $mobster->setParent($mobsterBoss);
        $mobsterBoss->addChild($mobster);
    }

    public function removeNode(MafiaNode $mobsterToRemove)
    {
        // TODO Discuss with SYX (should a node know how to remove itself from the tree?)
        $mobsterToRemove->removeFromOrganization();
    }

    public function compareNodeRanks(MafiaNode $first, MafiaNode $second) : int
    {
        $rankComparison = self::RANK_EQUAL;

        if ($first->getRank() < $second->getRank()){
            $rankComparison = self::FIRST_RANKS_HIGHER;
        }elseif ($first->getRank() > $second->getRank()){
            $rankComparison = self::SECOND_RANKS_HIGHER;
        }

        return $rankComparison;
    }

    public function shouldPutNodeUnderSpecialSurveillance(MafiaNode $node) : bool
    {
        return $node->hasNSubordinatesUnder(50);
    }

}