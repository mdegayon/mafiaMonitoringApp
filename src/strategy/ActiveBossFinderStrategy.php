<?php

namespace Src\strategy;

use Src\MafiaOrganization;
use Src\MafiaPosition;
use Src\MafiaState;
use Src\Mobster;
use Src\Prison;

class ActiveBossFinderStrategy
{
    private MafiaOrganization $org;
    private Prison $prison;

    public function __construct(MafiaOrganization $organization, Prison $prison)
    {
        $this->org = $organization;
        $this->prison = $prison;
    }

    public function findActiveBossFor(MafiaPosition $position): Mobster
    {
        $boss = $position->getBoss();
        while ( !$this->isEmptyOrHasActiveStatus($boss) ){
            $boss = $this->findBoss($boss);
        }

        $this->checkBossIsNotEmpty($boss);

        return $boss;
    }

    private function findBoss(Mobster $mobster) : Mobster
    {
        $boss = null;

        if ( $this->isMobsterInPrison($mobster)){
            $boss = $this->getReplacementOfImprisonedMobster($mobster);
        }else{
            $boss = $this->org->getBoss($mobster);
        }

        return $boss;
    }

    private function isMobsterInPrison(Mobster $mobster) : bool
    {
        return $mobster->getState() === MafiaState::Imprisoned;
    }

    private function getReplacementOfImprisonedMobster(Mobster $mobster) : Mobster
    {
        return $this->prison->getMobsterPosition($mobster)->getReplacement();
    }

    private function isEmptyOrHasActiveStatus(?Mobster $mobster) : bool
    {
        return  ($mobster === null) || ($mobster->getState() == MafiaState::Active);
    }

    private function checkBossIsNotEmpty(?Mobster $boss) : void
    {
        if ($boss === null){
            throw new \DomainException(
                "Can't find active boss for Mobster. ".
                "Either this mobster is Organization's Don or every boss is dead or imprisoned." .
                "Mobster: $boss"
            );
        }
    }
}