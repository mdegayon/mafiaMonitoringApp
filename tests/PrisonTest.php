<?php

use Src\Mobster;
use Src\Prison;
use PHPUnit\Framework\TestCase;
use Src\tree\mafia\MafiaTree;

class PrisonTest extends TestCase
{

    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem, $frankie;
    protected MafiaTree $corleoneTree;
    protected Prison $prison;

    protected function setUp(): void
    {
        $this->vito = new Mobster("Vito", "Corleone", "The Godfather", new \DateTime('1910-02-01'));

        $this->sonny = new Mobster("Santino", "Corleone", "Sonny", new \DateTime('1935-04-01'));
        $this->fredo = new Mobster("Frederico", "Corleone", "Fredo", new \DateTime('1938-01-01'));
        $this->mike = new Mobster("Michele", "Corleone", "Mike", new \DateTime('1940-06-01'));

        $this->clem = new Mobster("Peter", "Clemenza", "Fat Clemenza", new \DateTime('1918-10-01'));
        $this->frankie = new Mobster("Francesco", "Pentangeli", "Frankie 5 Angels", new \DateTime('1920-02-01'));

        $this->carlo = new Mobster("Carlo", "Rizzi", "", new \DateTime('1930-09-01'));

        $this->createCorleoneTree();

        $this->prison = new Prison($this->corleoneTree);
    }

    private function createCorleoneTree() : void
    {
        $this->corleoneTree = new MafiaTree($this->vito);

        $this->corleoneTree->addMobster($this->mike, $this->vito);
        $this->corleoneTree->addMobster($this->sonny, $this->vito);
        $this->corleoneTree->addMobster($this->fredo, $this->vito);

        $this->corleoneTree->addMobster($this->clem, $this->mike);

        $this->corleoneTree->addMobster($this->carlo, $this->clem);
        $this->corleoneTree->addMobster($this->frankie, $this->clem);
    }
    public function testImprisonMobster()
    {
        $imprisonedMobster = $this->carlo;

        $subordinates = $this->corleoneTree->getDirectSubordinates($imprisonedMobster);
        $boss = $this->corleoneTree->getBossOfMobster($imprisonedMobster);
        $replacement = $this->frankie;

        $this->prison->imprisonMobster(
            $imprisonedMobster,
            $boss,
            $subordinates,
            $replacement
        );

        $position = $this->prison->getMobsterPosition($imprisonedMobster);

        self::assertNotNull($position);
        self::assertInstanceOf(\Src\MafiaPosition::class, $position);

        self::assertEquals($boss, $position->getBoss());
        self::assertEquals($replacement, $position->getReplacement());
        self::assertEquals($subordinates, $position->getSubordinates());

        self::assertEquals(\Src\MafiaState::Imprisoned, $imprisonedMobster->getState());
    }

    public function testReleaseMobster()
    {
        $this->expectNotToPerformAssertions();
    }

}
