<?php

namespace Src\strategy;

use Src\tree\mafia\MafiaNode;
use Src\tree\mafia\MafiaTree;

class MobsterReplacementStrategy
{
    private MafiaTree $mafiaTree;

    public function __construct(MafiaTree $mafiaTree)
    {
        $this->mafiaTree = $mafiaTree;
    }

    public function replaceMobster(MafiaNode $mobsterToReplace, MafiaNode $replacementBoss) : void
    {
        $mobstersToRelocate = $mobsterToReplace->getDirectSubordinates();

        foreach ($mobstersToRelocate as $mobsterNode){

            $mobsterNode->setOriginalBoss($mobsterToReplace);
            $replacementBoss->addChild($mobsterNode);
        }

        $mobsterToReplace->setReplacementNode($replacementBoss);

        $this->mafiaTree->removeNode($mobsterToReplace);
    }

}