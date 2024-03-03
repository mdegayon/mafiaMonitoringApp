<?php

namespace Src;

use Src\tree\mafia\MafiaNode;
use Src\tree\mafia\MafiaTree;

class ReplacementBossResolver
{

    public  function findReplacementBossFor(MafiaNode $replacedBoss) : MafiaNode|null
    {
        $replacementBoss = $this->findReplacementFromPairs($replacedBoss);

        if (!$replacementBoss){
            $replacementBoss = $this->replaceWithBossesBoss($replacedBoss);
        }

        return $replacementBoss;
    }

    private function findReplacementFromPairs(MafiaNode $replacedBoss) : MafiaNode|null
    {   //TODO: Discuss with SYX
        $replacementBoss = null;

        if ( !$replacedBoss->hasBoss()){
            return $replacementBoss;
        }

        foreach ( $replacedBoss->getParent()->getChildren() as $replacementCandidate){

            if ($replacementCandidate == $replacedBoss){
                continue;
            }

            if (!$replacementBoss || $this->replacementCandidateIsOlderThan($replacementCandidate, $replacementBoss) ){
                $replacementBoss = $replacementCandidate;
            }

        }

        return $replacementBoss;
    }

    private function replacementCandidateIsOlderThan(MafiaNode $candidate, MafiaNode $other): bool
    {
        $candidateRecruitmentDate = $candidate->getData()->getRecruitmentDate();
        $otherRecruitmentDate = $other->getData()->getRecruitmentDate();

        return $candidateRecruitmentDate < $otherRecruitmentDate;
    }

    private function replaceWithBossesBoss(MafiaNode $replacedBoss) : MafiaNode|null
    {
        $replacementBoss = null;
        if ($replacedBoss->hasBoss()){
            $replacementBoss = $replacedBoss->getParent();
        }
        return $replacementBoss;
    }


}