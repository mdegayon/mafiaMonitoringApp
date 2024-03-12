<?php

namespace Src\strategy;

use Src\Mobster;
use Src\tree\mafia\MafiaTree;

class MobsterReplacementStrategy
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
            $this->mafiaTree->addMobster($mobster, $replacementBoss);
        }

        //$mobsterToReplace->setReplacementNode($replacementBoss); TODO: SYX Discuss

        $this->mafiaTree->removeMobster($mobsterToReplace);
    }

}