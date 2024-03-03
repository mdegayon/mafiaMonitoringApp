<?php

namespace tree\mafia;

use Src\Mobster;
use Src\tree\mafia\MafiaNode;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class MafiaNodeTest extends TestCase
{
    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem;
    protected MafiaNode $vitoNode, $mikeNode, $fredoNode, $sonnyNode, $carloNode, $clemNode;

    public function setUp() : void
    {
        $this->vito = new Mobster("Vito", "Corleone", "The Godfather", new \DateTime('1910-02-01'));
        $this->mike = new Mobster("Michele", "Corleone", "Mike", new \DateTime('1940-06-01'));
        $this->fredo = new Mobster("Frederico", "Corleone", "Fredo", new \DateTime('1938-01-01'));
        $this->sonny = new Mobster("Santino", "Corleone", "Sonny", new \DateTime('1935-04-01'));
        $this->carlo = new Mobster("Carlo", "Rizzi", "", new \DateTime('1930-09-01'));
        $this->clem = new Mobster("Peter", "Clemenza", "Fat Clemenza", new \DateTime('1918-10-01'));

        $this->vitoNode = new MafiaNode($this->vito);
        $this->mikeNode = new MafiaNode($this->mike);
        $this->fredoNode = new MafiaNode($this->fredo);
        $this->sonnyNode = new MafiaNode($this->sonny);
        $this->carloNode = new MafiaNode($this->carlo);
        $this->clemNode = new MafiaNode($this->clem);
    }

    private function createCorleoneTree() : void
    {
        $this->vitoNode->addChild($this->sonnyNode);
        $this->vitoNode->addChild($this->fredoNode);
        $this->vitoNode->addChild($this->mikeNode);

            $this->mikeNode->addChild($this->clemNode);

                $this->clemNode->addChild($this->carloNode);
    }

    public function tearDown() : void {}

    public function testMobsterOfNode() : void {
        $this->assertInstanceOf( Mobster::class, $this->vitoNode->getData());
    }

    public function testNodeAdd() : void {

        $this->vitoNode->addChild($this->mikeNode);
        $this->vitoNode->addChild($this->fredoNode);
        $this->vitoNode->addChild($this->sonnyNode);

        self::assertCount(3, $this->vitoNode->getChildren(), "Vito should have 3 subordinates");
        self::assertCount(0, $this->clemNode->getChildren());
    }

    public function testNodeRank(): void {

        $this->createCorleoneTree();

        self::assertEquals(1, $this->vitoNode->getRank());
        self::assertEquals(2,$this->mikeNode->getRank());
        self::assertEquals(3, $this->clemNode->getRank());
        self::assertEquals(4, $this->carloNode->getRank());
    }

    public function testChildrenCountWithThreshold() : void
    {
        $this->createCorleoneTree();

        self::assertTrue($this->carloNode->hasNSubordinatesUnder(0));
        self::assertTrue($this->sonnyNode->hasNSubordinatesUnder(0));
        self::assertTrue($this->fredoNode->hasNSubordinatesUnder(0));
        self::assertTrue( $this->clemNode->hasNSubordinatesUnder(1));
        self::assertTrue( $this->mikeNode->hasNSubordinatesUnder(2));

        self::assertTrue( $this->vitoNode->hasNSubordinatesUnder(0));
        self::assertTrue( $this->vitoNode->hasNSubordinatesUnder(1));
        self::assertTrue( $this->vitoNode->hasNSubordinatesUnder(2));
        self::assertTrue( $this->vitoNode->hasNSubordinatesUnder(3));
        self::assertTrue( $this->vitoNode->hasNSubordinatesUnder(4));
        self::assertTrue( $this->vitoNode->hasNSubordinatesUnder(5));
    }

    public function testNodeRemoval() : void
    {
        $this->createCorleoneTree();

        self::assertTrue( $this->isMobsterSubordinateOfBoss($this->sonnyNode, $this->vitoNode));
        $this->sonnyNode->removeFromOrganization();
        self::assertFalse( $this->isMobsterSubordinateOfBoss($this->sonnyNode, $this->vitoNode));

        self::assertTrue( $this->isMobsterSubordinateOfBoss($this->fredoNode, $this->vitoNode));
        $this->fredoNode->removeFromOrganization();
        self::assertFalse( $this->isMobsterSubordinateOfBoss($this->fredoNode, $this->vitoNode));

        self::assertTrue( $this->isMobsterSubordinateOfBoss($this->carloNode, $this->clemNode));
        $this->carloNode->removeFromOrganization();
        self::assertFalse( $this->isMobsterSubordinateOfBoss($this->carloNode, $this->clemNode));
    }

    private function isMobsterSubordinateOfBoss(MafiaNode $subordinate, MafiaNode $boss) : bool
    {
        return in_array($subordinate, $boss->getDirectSubordinates());
    }

    public function testCantRemoveNodeWithNoBoss() : void
    {
        $this->createCorleoneTree();

        $this->expectException(\DomainException::class);
        $this->vitoNode->removeFromOrganization();
    }

    public function testCantRemoveNodeWithChildren() : void
    {
        $this->createCorleoneTree();

        $this->expectException(\DomainException::class);
        $this->mikeNode->removeFromOrganization();
    }

}

