<?php

namespace Src;

use Src\tree\mafia\MafiaTree;

class Prison
{
    private array $imprisonedMobsters;

    public function imprisonMobster(Mobster $mobster,  Mobster $boss, array $subordinates, Mobster $substitute) : void
    {
        $mobster->setState(MafiaState::Imprisoned);

        $this->imprisonedMobsters[$mobster->getKey()] = [
            'mobster' => $mobster,
            'position' => new MafiaPosition(
                $boss,
                $this->filterSubordinates($subordinates),
                $substitute
            )
        ];
    }

    private function filterSubordinates($subordinates) : array
    {
        $filteredSubordinates = [];
        /* @var Mobster $subordinate */
        foreach ($subordinates as $subordinate){

            if (!$subordinate->HasReplacementBoss()){

                $subordinate->setHasReplacementBoss(true);
                $filteredSubordinates[$subordinate->getKey()] = $subordinate;
            }
        }

        return $filteredSubordinates;
    }

    public function releaseMobster(Mobster $mobster) : MafiaPosition
    {
        $mobsterKey = $mobster->getKey();
        if ( !array_key_exists($mobsterKey, $this->imprisonedMobsters) ){
            throw new \DomainException("Couldn't find mobster in prison. Mobster key: '{$mobsterKey}'");
        }

        $mobsterRow = $this->imprisonedMobsters[$mobster->getKey()];
        $mobsterRow['mobster']->setState(MafiaState::Active);

        unset($this->imprisonedMobsters[$mobster->getKey()]);

        return $mobsterRow['position'];
    }

    public function getMobsterPosition(Mobster $mobster) : MafiaPosition
    {
        $mobsterKey = $mobster->getKey();
        if ( !array_key_exists($mobsterKey, $this->imprisonedMobsters) ){
            throw new \DomainException("Couldn't find mobster in prison. Mobster key: '{$mobsterKey}'");
        }

        return $this->imprisonedMobsters[$mobsterKey]['position'];
    }

    public function isMobsterInPrison(Mobster $mobster) : bool
    {
        return array_key_exists($mobster->getKey(), $this->imprisonedMobsters);
    }

}