<?php

namespace tree\mafia;

use Src\Mobster;
use Src\tree\mafia\MafiaNode;
use Src\tree\mafia\MafiaTree;
use PHPUnit\Framework\TestCase;

class MafiaTreeTest extends TestCase
{
    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem, $frankie;
    protected MafiaNode $vitoNode, $mikeNode, $fredoNode, $sonnyNode, $carloNode, $clemNode, $frankieNode;
    protected MafiaTree $corleoneTree;

    public function setUp() : void
    {
        $this->vito = new Mobster("Vito", "Corleone", "The Godfather", new \DateTime('1910-02-01'));
        $this->mike = new Mobster("Michele", "Corleone", "Mike", new \DateTime('1940-06-01'));
        $this->fredo = new Mobster("Frederico", "Corleone", "Fredo", new \DateTime('1938-01-01'));
        $this->sonny = new Mobster("Santino", "Corleone", "Sonny", new \DateTime('1935-04-01'));
        $this->carlo = new Mobster("Carlo", "Rizzi", "", new \DateTime('1930-09-01'));
        $this->clem = new Mobster("Peter", "Clemenza", "Fat Clemenza", new \DateTime('1918-10-01'));
        $this->frankie = new Mobster("Francesco", "Pentangeli", "Frankie 5 Angels", new \DateTime('1920-02-01'));

        $this->vitoNode = new MafiaNode($this->vito);
        $this->fredoNode = new MafiaNode($this->fredo);
        $this->sonnyNode = new MafiaNode($this->sonny);
        $this->mikeNode = new MafiaNode($this->mike);
        $this->clemNode = new MafiaNode($this->clem);
        $this->carloNode = new MafiaNode($this->carlo);
        $this->frankieNode = new MafiaNode($this->frankie);

        $this->corleoneTree = new MafiaTree($this->vitoNode);
    }

    public function tearDown() : void {}

    private function createCorleoneTree() : void
    {
        $this->corleoneTree->addNode($this->mikeNode, $this->vitoNode);
        $this->corleoneTree->addNode($this->sonnyNode, $this->vitoNode);
        $this->corleoneTree->addNode($this->fredoNode, $this->vitoNode);

        $this->corleoneTree->addNode($this->clemNode, $this->mikeNode);

        $this->corleoneTree->addNode($this->carloNode, $this->clemNode);
        $this->corleoneTree->addNode($this->frankieNode, $this->clemNode);
    }

    private function recruitFiftySubordinatesForVito() : void
    {
        for ($i = 0; $i < 50; $i++){
            $this->corleoneTree->addNode(
                mobster: new MafiaNode(
                    data: new Mobster("firstName_$i", "lastName_$i", "nickname_$i", new \DateTime())
                ),
                mobsterBoss: $this->vitoNode
            );
        }
    }

    public function testAddNodes() : void
    {
        $this->expectNotToPerformAssertions();
        $this->createCorleoneTree();
    }

    public function testDon() : void
    {
        $this->createCorleoneTree();
        self::assertEquals($this->corleoneTree->getDon(), $this->vitoNode);
    }

    public function testRank() : void
    {
        $this->createCorleoneTree();
        self::assertEquals(MafiaTree::RANK_EQUAL, $this->corleoneTree->compareNodeRanks($this->sonnyNode, $this->mikeNode));
        self::assertEquals(MafiaTree::RANK_EQUAL, $this->corleoneTree->compareNodeRanks($this->frankieNode, $this->carloNode));
        self::assertEquals(MafiaTree::FIRST_RANKS_HIGHER, $this->corleoneTree->compareNodeRanks($this->vitoNode, $this->mikeNode));
    }

    public function testSpecialSurveillance() : void
    {
        $this->createCorleoneTree();

        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->vitoNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->mikeNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->fredoNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->sonnyNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->clemNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->frankieNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->carloNode));

        $this->recruitFiftySubordinatesForVito();

        self::assertTrue($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->vitoNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->mikeNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->fredoNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->sonnyNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->clemNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->frankieNode));
        self::assertFalse($this->corleoneTree->shouldPutNodeUnderSpecialSurveillance($this->carloNode));
    }

}
