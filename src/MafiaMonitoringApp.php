<?php

namespace Src;

use Src\strategy\ActiveBossFinderStrategy;

class MafiaMonitoringApp
{
    const SUBORDINATES_COUNT_FOR_SPECIAL_SURVEILLANCE = 50;

    const   FIRST_RANKS_HIGHER = 1,
            SECOND_RANKS_HIGHER = -1,
            RANK_EQUAL = 0;

    private Prison $prison;
    private MafiaOrganization $mafiaOrganization;

    private ActiveBossFinderStrategy $bossFinder;

    public function __construct(Mobster $theDon)
    {
        $this->mafiaOrganization = new MafiaOrganization($theDon);
        $this->prison = new Prison();

        $this->bossFinder = new ActiveBossFinderStrategy($this->mafiaOrganization, $this->prison);
    }

    public function addMobster(Mobster $mobster, Mobster $boss) : void
    {
        $this->mafiaOrganization->addMobster($mobster, $boss);
    }

    public function countMobstersInOrganization() : int
    {
        return $this->mafiaOrganization->countMobstersInOrganization();
    }

    public function isMobsterInPrison(Mobster $mobster) : bool
    {
        return $this->prison->isMobsterInPrison($mobster);
    }

    public function mobsterBelongsToOrganization(Mobster $mobster) : bool
    {
        return $this->mafiaOrganization->mobsterBelongsToOrganization($mobster);
    }

    public function getSubordinates(Mobster $mobster) : array
    {
        return $this->mafiaOrganization->getDirectSubordinateS($mobster);
    }

    public function getBoss(Mobster $mobster) : ?Mobster
    {
        return $this->mafiaOrganization->getBoss($mobster);
    }

    public function sendToPrison(Mobster $imprisonedMobster) : void
    {
        $boss = $this->mafiaOrganization->getBoss($imprisonedMobster);
        $subordinates = $this->mafiaOrganization->getDirectSubordinateS($imprisonedMobster);
        $replacement = $this->mafiaOrganization->replaceImprisonedMobster($imprisonedMobster);

        $this->prison->imprisonMobster($imprisonedMobster, $boss, $subordinates, $replacement);
    }

    public function releaseFromPrison (Mobster $releasedMobster) : void
    {
        $oldPosition = $this->prison->releaseMobster($releasedMobster);
        $activeBoss = $this->findActiveBossFromPosition($oldPosition);

        $oldPosition->setBoss($activeBoss);

        $this->mafiaOrganization->recoverMobsterPosition($releasedMobster, $oldPosition);
    }

    private function findActiveBossFromPosition(MafiaPosition $position) : Mobster
    {
        return $this->bossFinder->findActiveBossFor($position);
    }

    public function shouldPutUnderSpecialSurveillance(Mobster $mobster) : bool
    {
        $mobsterSubordinatesCount = $this->mafiaOrganization->countSubordinatesWithThreshold(
            $mobster,
            self::SUBORDINATES_COUNT_FOR_SPECIAL_SURVEILLANCE
        );
        return $mobsterSubordinatesCount >= self::SUBORDINATES_COUNT_FOR_SPECIAL_SURVEILLANCE;
    }

    public function compareMobsterRanks(Mobster $first, Mobster $second) : int
    {
        $rankComparison = self::RANK_EQUAL;

        $firstMobsterRank = $this->mafiaOrganization->getRank($first);
        $secondMobsterRank = $this->mafiaOrganization->getRank($second);

        if ( $firstMobsterRank < $secondMobsterRank ){
            $rankComparison = self::FIRST_RANKS_HIGHER;
        }elseif ( $secondMobsterRank < $firstMobsterRank ){
            $rankComparison = self::SECOND_RANKS_HIGHER;
        }

        return $rankComparison;
    }

    public function print() : void
    {
        $this->mafiaOrganization->print();
    }

}