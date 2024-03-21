<?php

namespace Src\strategy;

use Src\MafiaPosition;
use Src\MafiaState;
use Src\Mobster;
use Src\tree\mafia\MafiaTree;

class PositionRecoveryStrategy
{
    private MafiaTree $mafiaTree;

    public function __construct(MafiaTree $mafiaTree)
    {
        $this->mafiaTree = $mafiaTree;
    }

    public function recoverPositionOf(Mobster $releasedMobster, MafiaPosition $position) : void
    {
        $boss = $position->getBoss();
        $subordinates = $position->getSubordinates();

        $this->getMobsterBackIntoOrganization($releasedMobster, $boss);

        $this->reassignMobsterSubordinates($releasedMobster, $subordinates);
    }

    private function reassignMobsterSubordinates(Mobster $releasedMobster, array $subordinates) : void
    {
        /* @var Mobster $subordinate */
        foreach ($subordinates as $subordinate){
            $this->mafiaTree->moveMobster($subordinate, $releasedMobster);
        }
    }

    private function getMobsterBackIntoOrganization(Mobster $releasedMobster, Mobster $boss) : void
    {
        $this->mafiaTree->addMobster($releasedMobster, $boss);
    }

}