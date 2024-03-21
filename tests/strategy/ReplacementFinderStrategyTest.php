<?php

namespace strategy;

use Src\Mobster;
use Src\strategy\ReplacementFinderStrategy;
use PHPUnit\Framework\TestCase;
use Src\tree\mafia\MafiaTree;

class ReplacementFinderStrategyTest extends TestCase
{

    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem, $frankie;
    protected MafiaTree $corleoneTree;

    protected ReplacementFinderStrategy $resolver;

    public function setUp() : void
    {
        $this->vito = new Mobster("Vito", "Corleone", "The Godfather", new \DateTime('1910-02-01'));

        $this->sonny = new Mobster("Santino", "Corleone", "Sonny", new \DateTime('1935-04-01'));
        $this->fredo = new Mobster("Frederico", "Corleone", "Fredo", new \DateTime('1938-01-01'));
        $this->mike = new Mobster("Michele", "Corleone", "Mike", new \DateTime('1940-06-01'));

        $this->clem = new Mobster("Peter", "Clemenza", "Fat Clemenza", new \DateTime('1918-10-01'));
        $this->frankie = new Mobster("Francesco", "Pentangeli", "Frankie 5 Angels", new \DateTime('1920-02-01'));

        $this->carlo = new Mobster("Carlo", "Rizzi", "", new \DateTime('1930-09-01'));

        $this->createCorleoneTree();

        $this->resolver = new ReplacementFinderStrategy($this->corleoneTree);
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

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testNodeWithoutParentSubstitution() : void
    {
        $replacementNode = $this->resolver->findReplacementFor($this->vito);
        $this->assertNull($replacementNode);
    }

    public function testReplaceBoss() : void
    {
        self::assertEquals(
            $this->sonny,
            $this->resolver->findReplacementFor($this->mike)
        );
        self::assertEquals(
            $this->fredo,
            $this->resolver->findReplacementFor($this->sonny)
        );
        self::assertEquals(
            $this->mike,
            $this->resolver->findReplacementFor($this->clem)
        );
        self::assertEquals(
            $this->carlo,
            $this->resolver->findReplacementFor($this->frankie)
        );
        self::assertEquals(
            $this->frankie,
            $this->resolver->findReplacementFor($this->carlo)
        );

    }


}
