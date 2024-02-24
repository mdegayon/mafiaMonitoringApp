<?php

namespace Src;

class MafiaPosition {

    private \Src\Mobster $boss;
    private array $subordinates;

    public function __construct(Mobster $boss, array $subordinates) {
        $this->boss = $boss;
        $this->subordinates = $subordinates;
    }

    public function getSubordinates(): array
    {
        return $this->subordinates;
    }
    
    public function getBoss() : Mobster
    {
        return $this->boss;
    }

}
