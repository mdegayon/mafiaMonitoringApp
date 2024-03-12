<?php declare(strict_types=1);

namespace Src;

use Src\strategy\ActiveBossFinderStrategy;
use Src\strategy\MobsterReplacementStrategy;
use Src\strategy\PositionRecoveryStrategy;
use Src\tree\mafia\MafiaTree;
use Src\tree\Node;

class MafiaOrganization {

    const SUBORDINATES_COUNT_FOR_SPECIAL_SURVEILLANCE = 50;

    const   FIRST_RANKS_HIGHER = 1,
            SECOND_RANKS_HIGHER = -1,
            RANK_EQUAL = 0;

    private array $mobsters;
    private MafiaTree $mafiaTree;

    private ReplacementBossResolver $resolver;
    private MobsterReplacementStrategy $replacementStrategy;
    private PositionRecoveryStrategy $positionRecoveryStrategy;

    public function __construct(Mobster $theDon)
    {
        $this->mafiaTree = new MafiaTree($theDon);
        $this->addMobsterToList($theDon->getKey(), $theDon);

        $this->resolver = new ReplacementBossResolver($this->mafiaTree);
        $this->replacementStrategy = new MobsterReplacementStrategy($this->mafiaTree);
        $this->positionRecoveryStrategy = new PositionRecoveryStrategy(new ActiveBossFinderStrategy($this->mafiaTree));
    }

    private function addMobsterToList(string $key, Mobster $mobster) : void
    {
        $this->mobsters[$key] = $mobster;
    }

    public function addMobster(Mobster $mobster, Mobster $boss) : void
    {
        $this->mafiaTree->addMobster($mobster, $boss);
        $this->addMobsterToList($mobster->getKey(), $mobster);
    }

    public function countMobstersInOrganization() : int
    {
        return sizeof($this->mobsters);
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
        if (!array_key_exists($key, $this->mobsters)){
            throw new \DomainException("Mobster with key=$key is missing from our records");
        }
        return $this->mobsters[$key];
    }

    /*
    public function sendToPrison(Mobster $imprisonedMobster) : void
    {
        $imprisonedMobsterNode = $this->findMobsterInList($imprisonedMobster->getKey());
        $imprisonedMobster->setState(MafiaState::Imprisoned);
        // TODO Discuss with SYX (Should findReplacementBossFor throw the exception itself already ?)
        $replacementBoss = $this->resolver->findReplacementBossFor($imprisonedMobsterNode);
        if ($replacementBoss === Node::EMPTY_NODE){
            throw new \DomainException("Can't find replacement boss for Mobster. Can't send him to prison. Mobster: $imprisonedMobster");
        }
        $this->replacementStrategy->replaceMobster($imprisonedMobsterNode, $replacementBoss);
    }

    public function releaseFromPrison (Mobster $releasedMobster) : void
    {
        $releasedMobsterNode = $this->findMobsterInList($releasedMobster->getKey());
        $releasedMobster->setState(MafiaState::Active);

        $this->positionRecoveryStrategy->recoverPositionOf($releasedMobsterNode);
    }
    */

}
