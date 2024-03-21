<?php

namespace Src\strategy;

use Src\Mobster;
use Src\tree\mafia\MafiaTree;

class ReplacementFinderStrategy
{

    private MafiaTree $mafiaTree;

    public function __construct(MafiaTree $mafiaTree)
    {
        $this->mafiaTree = $mafiaTree;
    }

    public  function findReplacementFor(Mobster $replacedBoss) : ?Mobster
    {
        $replacementBoss = $this->findReplacementFromPairs($replacedBoss);

        if (!$replacementBoss){
            $replacementBoss = $this->replaceWithBossesBoss($replacedBoss);
        }

        return $replacementBoss;
    }

    private function findReplacementFromPairs(Mobster $replacedBoss) : ?Mobster
    {   //TODO: Discuss with SYX
        $replacementBoss = null;

        if ( !$this->mobsterHasBoss($replacedBoss)){
            return $replacementBoss;
        }

        $replacedBossesBoss = $this->getMobsterBoss($replacedBoss);
        $pairs = $this->getMobsterChildren($replacedBossesBoss);
        foreach ( $pairs as $replacementCandidate){

            if ($replacementCandidate == $replacedBoss){
                continue;
            }

            if (!$replacementBoss || $this->replacementCandidateIsOlderThan($replacementCandidate, $replacementBoss) ){
                $replacementBoss = $replacementCandidate;
            }

        }

        return $replacementBoss;
    }

    private function mobsterHasBoss(Mobster $mobster) : bool
    {
        return !is_null( $this->getMobsterBoss($mobster) );
    }

    private function getMobsterBoss(Mobster $mobster) : ?Mobster
    {
        return $this->mafiaTree->getBossOfMobster($mobster);
    }

    private function getMobsterChildren(Mobster $mobster) : array
    {
        return $this->mafiaTree->getDirectSubordinates($mobster);
    }

    private function replacementCandidateIsOlderThan(Mobster $candidate, Mobster $other): bool
    {
        return $candidate->getRecruitmentDate() < $other->getRecruitmentDate();
    }

    private function replaceWithBossesBoss(Mobster $replacedBoss) : ?Mobster
    {
        $replacementBoss = null;
        if (  $this->mobsterHasBoss($replacedBoss) ){
            $replacementBoss = $this->getMobsterBoss($replacedBoss);
        }
        return $replacementBoss;
    }


}