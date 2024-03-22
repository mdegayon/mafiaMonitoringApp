<?php

namespace Src;

class MafiaPosition {

    private Mobster $boss;
    private ?Mobster $replacement;
    private array $subordinates;

    public function __construct(Mobster $boss, array $subordinates, Mobster $replacement = null) {
        $this->boss = $boss;
        $this->subordinates = $subordinates;
        $this->replacement = $replacement;
    }

    public function getSubordinates(): array
    {
        return $this->subordinates;
    }
    
    public function getBoss() : Mobster
    {
        return $this->boss;
    }

    public function getReplacement(): ?Mobster
    {
        return $this->replacement;
    }

    public function setBoss(Mobster $boss) : void
    {
        $this->boss = $boss;
    }

}
