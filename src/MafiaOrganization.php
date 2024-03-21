<?php declare(strict_types=1);

namespace Src;

use Src\strategy\ActiveBossFinderStrategy;
use Src\strategy\ReplacementFinderStrategy;
use Src\strategy\ReplacingMobsterStrategy;
use Src\strategy\PositionRecoveryStrategy;
use Src\tree\mafia\MafiaTree;
use Src\tree\Node;

class MafiaOrganization {

    const SUBORDINATES_COUNT_FOR_SPECIAL_SURVEILLANCE = 50;

    const   FIRST_RANKS_HIGHER = 1,
            SECOND_RANKS_HIGHER = -1,
            RANK_EQUAL = 0;

    private MafiaTree $mafiaTree;

    private ReplacementFinderStrategy $replacementFinder;
    private ReplacingMobsterStrategy $replacementStrategy;
    private PositionRecoveryStrategy $positionRecoveryStrategy;

    public function __construct(Mobster $theDon)
    {
        $this->mafiaTree = new MafiaTree($theDon);

        $this->replacementFinder = new ReplacementFinderStrategy($this->mafiaTree);
        $this->replacementStrategy = new ReplacingMobsterStrategy($this->mafiaTree);
        $this->positionRecoveryStrategy = new PositionRecoveryStrategy($this->mafiaTree);
    }

    public function addMobster(Mobster $mobster, Mobster $boss) : void
    {
        $node = $this->mafiaTree->addMobster($mobster, $boss);
    }

    public function countMobstersInOrganization() : int
    {
        return $this->mafiaTree->countMobsters();
    }

    public function getBoss(Mobster $mobster) : Mobster
    {
        return $this->mafiaTree->getBossOfMobster($mobster);
    }

    public function getDirectSubordinateS(Mobster $mobster) : array
    {
        return $this->mafiaTree->getDirectSubordinates($mobster);
    }

    public function shouldPutUnderSpecialSurveillance(Mobster $mobster) : bool
    {
        $mobsterSubordinatesCount = $this->mafiaTree->countSubordinatesWithThreshold(
            $mobster,
            self::SUBORDINATES_COUNT_FOR_SPECIAL_SURVEILLANCE
        );
        return $mobsterSubordinatesCount >= self::SUBORDINATES_COUNT_FOR_SPECIAL_SURVEILLANCE;
    }

    public function compareMobsterRanks(Mobster $first, Mobster $second) : int
    {
        $rankComparison = self::RANK_EQUAL;

        if ( $this->mafiaTree->getRank($first) < $this->mafiaTree->getRank($second) ){
            $rankComparison = self::FIRST_RANKS_HIGHER;
        }elseif ( $this->mafiaTree->getRank($second) < $this->mafiaTree->getRank($first) ){
            $rankComparison = self::SECOND_RANKS_HIGHER;
        }

        return $rankComparison;
    }

    private function findMobsterInList(string $key) : Mobster
    {
        if (!array_key_exists($key, $this->mobsterNodes)){
            throw new \DomainException("Mobster with key=$key is missing from our records");
        }
        return $this->mobsterNodes[$key]->getData();
    }

    private function findNodeInList(string $key) : Node
    {
        if (!array_key_exists($key, $this->mobsterNodes)){
            throw new \DomainException("Mobster with key=$key is missing from our records");
        }
        return $this->mobsterNodes[$key];
    }

    public function replaceImprisonedMobster(Mobster $imprisonedMobster) : Mobster
    {
        // TODO Discuss with SYX (Should findReplacementBossFor throw the exception itself already ?)
        $replacement = $this->replacementFinder->findReplacementFor($imprisonedMobster);
        if ($replacement === Node::EMPTY_NODE){
            throw new \DomainException("Can't find replacement boss for Mobster. Can't send him to prison. Mobster: $imprisonedMobster");
        }

        $this->replacementStrategy->replaceMobster($imprisonedMobster, $replacement);

        return $replacement;
    }

    public function recoverMobsterPosition (Mobster $releasedMobster, MafiaPosition $position) : void
    {
        $this->positionRecoveryStrategy->recoverPositionOf($releasedMobster, $position);
    }

    public function mobsterBelongsToOrganization(Mobster $mobster) : bool
    {
        return $this->mafiaTree->contains($mobster);
    }

    public function print() : void
    {
        $this->mafiaTree->print();
    }
}
