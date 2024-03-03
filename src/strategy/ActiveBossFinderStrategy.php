<?php

namespace Src\strategy;

use Src\MafiaState;
use Src\tree\mafia\MafiaNode;
use Src\tree\mafia\MafiaTree;
use Src\tree\Node;

class ActiveBossFinderStrategy
{
    private MafiaTree $mafiaTree;

    private $mafiaNode;

    public function __construct(MafiaTree $mafiaTree)
    {
        $this->mafiaTree = $mafiaTree;
    }

    public function findActiveBossFor(MafiaNode $mafiaNode): MafiaNode
    {
        $this->mafiaNode = $mafiaNode;

        $activeBoss = $mafiaNode->getParent();
        while ( !$this->isAnEmptyNodeOrHasActiveStatus($activeBoss) ){
            $activeBoss = $activeBoss->getParent();
        }

        $this->throwExceptionIfBossIsEmpty($activeBoss);

        return $activeBoss;
    }

    private function isAnEmptyNodeOrHasActiveStatus(MafiaNode $bossNode) : bool
    {
        return  $bossNode == Node::EMPTY_NODE ||
                $bossNode->getData()->getState == MafiaState::Active;
    }

    private function throwExceptionIfBossIsEmpty(MafiaNode $boss) : void
    {
        if ($boss === Node::EMPTY_NODE){
            throw new \DomainException(
                "Can't find active boss for Node. ".
                "Either node is Organization's Don or every boss is dead or imprisoned." .
                "Node: $this->mafiaNode"
            );
        }
    }
}