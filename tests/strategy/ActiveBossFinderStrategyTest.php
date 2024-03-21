<?php

namespace strategy;

use Src\MafiaOrganization;
use Src\Mobster;
use Src\Prison;
use Src\strategy\ActiveBossFinderStrategy;
use PHPUnit\Framework\TestCase;
use Src\tree\mafia\MafiaTree;

class ActiveBossFinderStrategyTest extends TestCase
{

    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem, $frankie;
    protected MafiaOrganization $corleonesi;

    protected Prison $prison;

    private ActiveBossFinderStrategy $strategy;

    protected function setUp(): void
    {
        $this->vito = new Mobster("Vito", "Corleone", "The Godfather", new \DateTime('1910-02-01'));

        $this->sonny = new Mobster("Santino", "Corleone", "Sonny", new \DateTime('1935-04-01'));
        $this->fredo = new Mobster("Frederico", "Corleone", "Fredo", new \DateTime('1938-01-01'));
        $this->mike = new Mobster("Michele", "Corleone", "Mike", new \DateTime('1940-06-01'));

        $this->clem = new Mobster("Peter", "Clemenza", "Fat Clemenza", new \DateTime('1918-10-01'));
        $this->frankie = new Mobster("Francesco", "Pentangeli", "Frankie 5 Angels", new \DateTime('1920-02-01'));

        $this->carlo = new Mobster("Carlo", "Rizzi", "", new \DateTime('1930-09-01'));

        $this->createCorleoneFamily();

        $this->strategy = new ActiveBossFinderStrategy();
    }

    private function createCorleoneFamily() : void
    {
        $this->corleonesi = new MafiaOrganization($this->vito);

        $this->corleonesi->addMobster($this->mike, $this->vito);
        $this->corleonesi->addMobster($this->sonny, $this->vito);
        $this->corleonesi->addMobster($this->fredo, $this->vito);

        $this->corleonesi->addMobster($this->clem, $this->mike);

        $this->corleonesi->addMobster($this->carlo, $this->clem);
        $this->corleonesi->addMobster($this->frankie, $this->clem);
    }

    public function testCantReplace() : void
    {
        $this->expectException(\DomainException::class);
        $this->strategy->findActiveBossFor($this->vito, $this->clem);
    }

    public function testReplaceWithNoSubordinates() : void
    {
        self::assertContains( $this->fredo, $this->corleoneTree->getDirectSubordinates($this->vito) );
        self::assertEquals($this->vito, $this->corleoneTree->getBossOfMobster($this->fredo));
        $this->strategy->replaceMobster($this->fredo, $this->sonny);
        self::assertNotContains($this->fredo, $this->corleoneTree->getDirectSubordinates($this->vito));
//        self::assertEquals($this->sonny, $this->fredo->getReplacementNode());
        $this->expectException(DomainException::class);
        self::assertEquals(Node::EMPTY_NODE, $this->corleoneTree->getBossOfMobster($this->fredo));
    }

}
