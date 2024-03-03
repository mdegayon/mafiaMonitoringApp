<?php

namespace Src\strategy;

use Src\MafiaState;
use Src\tree\mafia\MafiaNode;

class PositionRecoveryStrategy
{
    private ActiveBossFinderStrategy $bossFinderStrategy;

    public function __construct(ActiveBossFinderStrategy $bossFinderStrategy)
    {
        $this->bossFinderStrategy = $bossFinderStrategy;
    }

    public function recoverPositionOf(MafiaNode $releasedMobsterNode) : void
    {
        $this->reassignSubordinates($releasedMobsterNode);
        $this->reassignBoss($releasedMobsterNode);
    }

    private function reassignSubordinates(MafiaNode $releasedMobsterNode) : void
    {
        foreach ($releasedMobsterNode->getDirectSubordinates() as $subordinate){

            $temporaryBoss = $subordinate->getParent()->removeChild($subordinate);
            $temporaryBoss->removeChild($subordinate);

            $subordinate->setParent($releasedMobsterNode);
        }
    }

    private function reassignBoss(MafiaNode $releasedMobsterNode) : void
    {
        $newBoss = $this->bossFinderStrategy->findActiveBossFor($releasedMobsterNode);
        $releasedMobsterNode->setParent($newBoss);
    }

}