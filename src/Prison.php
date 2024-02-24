<?php declare(strict_types=1);

namespace Src;

class Prison {
    
    private array $prisonerOldPositions;

    public function __construct()
    {
        $this->prisonerOldPositions = [];
    }

    public function sendToPrison(Mobster $mobster) : void {

        $mobster->setState(MafiaState::Imprisoned);
        $this->addPosition($mobster);

        if ($this->MobsterHasSubordinates($mobster)){

            $newBoss = $this->chooseNewBoss($mobster);
            $mobster->changeBossOfSubordinates($newBoss);
        }

    }

    private function addPosition(Mobster $mobster) : void {
        $this->prisonerOldPositions[$mobster->getId()] =  new MafiaPosition(
            $mobster->getBoss(),
            $mobster->getSubordinates()
        );
    }

    private function chooseNewBoss(Mobster $mobster) : Mobster {

        $newBoss = $this->chooseNewBossFromPairs($mobster);

        if ($newBoss === null){
            $newBoss = $this->chooseNewBossFromSubordinates($mobster);
        }

        return  $newBoss;
    }

    private function chooseNewBossFromPairs(Mobster $mobster) : Mobster|null{

        $newBoss = null;
        $boss = $mobster->getBoss();

        if ($boss !== null){
            $boss->removeSubordinate($mobster);
            $newBoss = $boss->getOldestSubordinate();
        }

        return $newBoss;
    }

    private function chooseNewBossFromSubordinates(Mobster $mobster) : Mobster|null{

        $newBoss = $mobster->getOldestSubordinate();
        $mobster->removeSubordinate($newBoss);

        return $newBoss;
    }

    private function MobsterHasSubordinates(Mobster $mobster) : bool {
        return $mobster->countDirectSubordinates() > 0;
    }

    public function releaseFromPrison(Mobster $mobster) : void {
        
        $mobster->setState(MafiaState::Active);

        $this->restoreBoss($mobster);
        $this->restoreSubordinates($mobster);

        $this->removePosition($mobster);
    }

    private function restoreBoss(Mobster $mobster) : void {
        $oldBoss = $this->findOldBoss($mobster);
        $oldBoss->addSubordinate($mobster);
    }

    private function restoreSubordinates(Mobster $mobster) : void {
        $oldSubordinates = $this->findOldSubordinates($mobster);
        foreach ($oldSubordinates as $subordinate) {
            $mobster->addSubordinate($subordinate);
        }
    }

    private function findOldBoss(Mobster $mobster) : Mobster {
        return $this->prisonerOldPositions[$mobster->getId()]->getBoss();
    }

    private function findOldSubordinates(Mobster $mobster) : array {
        return $this->prisonerOldPositions[$mobster->getId()]->getSubordinates();
    }

    private function removePosition(Mobster $mobster) : void {
        unset( $this->prisonerOldPositions[$mobster->getId()] );
    }
    
}
