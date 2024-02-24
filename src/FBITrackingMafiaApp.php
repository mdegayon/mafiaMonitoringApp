<?php

namespace Src;

use Src\Prison;
use Src\Mobster;
use Src\MafiaTree;

class FBITrackingMafiaApp {

    private Prison $prison;
    private MafiaTree $tree;

    const SPECIAL_SURVEILLANCE_SUBS_COUNT = 50;

    public function __construct()
    {
        $this->prison = new Prison();
        $this->tree = new MafiaTree();
    }

    public function addMobster(String $firstName, String $lastName, String $nickname, int $bossId) : void
    {
        $this->tree->addMobster( $firstName, $lastName, $nickname,$bossId);
    }

    public function getMobster($id) :Mobster{
        return $this->tree->getMobster($id);
    }

    public function sendToPrison(Mobster $mobster) : void {
        $this->prison->sendToPrison($mobster);
    }

    public function releaseFromPrison(Mobster $mobster) : void {
        $this->releaseFromPrison($mobster);
    }

    public function shouldPutUnderSpecialSurveillance(Mobster $mobster) : bool{
        return $mobster->countSubordinates() > self::SPECIAL_SURVEILLANCE_SUBS_COUNT;
    }
    
    public function getMemberWithHigherRank(Mobster $first, Mobster $second) : Mobster{
        return $first;
    }    
    
}
