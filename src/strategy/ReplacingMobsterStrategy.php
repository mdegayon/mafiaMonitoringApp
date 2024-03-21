<?php

namespace Src\strategy;

use Src\Mobster;
use Src\tree\mafia\MafiaTree;

class ReplacingMobsterStrategy
{
    private MafiaTree $mafiaTree;

    public function __construct(MafiaTree $mafiaTree)
    {
        $this->mafiaTree = $mafiaTree;
    }

    public function replaceMobster(Mobster $mobsterToReplace, Mobster $replacementBoss) : void
    {
        $mobstersToRelocate = $this->mafiaTree->getDirectSubordinates($mobsterToReplace);

        foreach ($mobstersToRelocate as $mobster){
            $this->mafiaTree->moveMobster($mobster, $replacementBoss);
        }

        $this->mafiaTree->removeMobster($mobsterToReplace);
    }

}