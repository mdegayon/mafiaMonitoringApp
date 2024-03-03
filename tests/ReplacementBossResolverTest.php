<?php

use Src\Mobster;
use Src\ReplacementBossResolver;
use PHPUnit\Framework\TestCase;
use Src\tree\mafia\MafiaNode;
use Src\tree\mafia\MafiaTree;

class ReplacementBossResolverTest extends TestCase
{

    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem, $frankie;
    protected MafiaNode $vitoNode, $mikeNode, $fredoNode, $sonnyNode, $carloNode, $clemNode, $frankieNode;
    protected MafiaTree $corleoneTree;

    protected ReplacementBossResolver $resolver;

    public function setUp() : void
    {
        $this->vito = new Mobster("Vito", "Corleone", "The Godfather", new \DateTime('1910-02-01'));

        $this->sonny = new Mobster("Santino", "Corleone", "Sonny", new \DateTime('1935-04-01'));
        $this->fredo = new Mobster("Frederico", "Corleone", "Fredo", new \DateTime('1938-01-01'));
        $this->mike = new Mobster("Michele", "Corleone", "Mike", new \DateTime('1940-06-01'));

        $this->clem = new Mobster("Peter", "Clemenza", "Fat Clemenza", new \DateTime('1918-10-01'));
        $this->frankie = new Mobster("Francesco", "Pentangeli", "Frankie 5 Angels", new \DateTime('1920-02-01'));

        $this->carlo = new Mobster("Carlo", "Rizzi", "", new \DateTime('1930-09-01'));

        $this->vitoNode = new MafiaNode($this->vito);
        $this->fredoNode = new MafiaNode($this->fredo);
        $this->sonnyNode = new MafiaNode($this->sonny);
        $this->mikeNode = new MafiaNode($this->mike);
        $this->clemNode = new MafiaNode($this->clem);
        $this->carloNode = new MafiaNode($this->carlo);
        $this->frankieNode = new MafiaNode($this->frankie);

        $this->createCorleoneTree();

        $this->resolver = new ReplacementBossResolver();
    }

    private function createCorleoneTree() : void
    {
        $this->corleoneTree = new MafiaTree($this->vitoNode);

        $this->corleoneTree->addNode($this->sonnyNode, $this->vitoNode);
        $this->corleoneTree->addNode($this->fredoNode, $this->vitoNode);
        $this->corleoneTree->addNode($this->mikeNode, $this->vitoNode);

            $this->corleoneTree->addNode($this->frankieNode, $this->mikeNode);
            $this->corleoneTree->addNode($this->clemNode, $this->mikeNode);

                $this->corleoneTree->addNode($this->carloNode, $this->clemNode);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testNodeWithoutParentSubstitution() : void
    {
        $replacementNode = $this->resolver->findReplacementBossFor($this->vitoNode);
        $this->assertNull($replacementNode);
    }

    public function testReplaceBoss() : void
    {
        self::assertEquals(
            $this->sonnyNode,
            $this->resolver->findReplacementBossFor($this->mikeNode)
        );

        self::assertEquals(
            $this->fredoNode,
            $this->resolver->findReplacementBossFor($this->sonnyNode)
        );

        self::assertEquals(
            $this->frankieNode,
            $this->resolver->findReplacementBossFor($this->clemNode)
        );

        self::assertEquals(
            $this->clemNode,
            $this->resolver->findReplacementBossFor($this->frankieNode)
        );

        self::assertEquals(
            $this->clemNode,
            $this->resolver->findReplacementBossFor($this->carloNode)
        );

    }
}
