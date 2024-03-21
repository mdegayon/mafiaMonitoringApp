<?php

namespace tree\mafia;

use Src\Mobster;
use Src\tree\mafia\MafiaTree;
use PHPUnit\Framework\TestCase;
use Src\tree\Node;use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class MafiaTreeTest extends TestCase
{
    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem, $frankie;
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

        $this->corleoneTree = new MafiaTree($this->vito);

        $this->createCorleoneTree();
    }

    public function tearDown() : void {}

    private function createCorleoneTree() : void
    {
        $this->corleoneTree->addMobster($this->mike, $this->vito);
        $this->corleoneTree->addMobster($this->sonny, $this->vito);
        $this->corleoneTree->addMobster($this->fredo, $this->vito);

        $this->corleoneTree->addMobster($this->clem, $this->mike);

        $this->corleoneTree->addMobster($this->carlo, $this->clem);
        $this->corleoneTree->addMobster($this->frankie, $this->clem);
    }

    private function recruitFiftySubordinatesForVito() : void
    {
        for ($i = 0; $i < 50; $i++){
            $this->corleoneTree->addMobster(
                new Mobster("firstName_$i", "lastName_$i", "nickname_$i", new \DateTime()),
                $this->vito
            );
        }
    }

    public function testAdd() : void
    {
        self::assertEquals(7, $this->corleoneTree->countMobsters());
        self::assertTrue( $this->corleoneTree->contains($this->vito));
        self::assertTrue( $this->corleoneTree->contains($this->mike));
        self::assertTrue( $this->corleoneTree->contains($this->sonny));
        self::assertTrue( $this->corleoneTree->contains($this->fredo));
        self::assertTrue( $this->corleoneTree->contains($this->clem));
        self::assertTrue( $this->corleoneTree->contains($this->frankie));
        self::assertTrue( $this->corleoneTree->contains($this->carlo));
    }

    public function testDon() : void
    {
        self::assertEquals($this->corleoneTree->getDon(), $this->vito);
    }

    public function testRank() : void
    {
        self::assertEquals( 1, $this->corleoneTree->getRank($this->vito));
        self::assertEquals( 2, $this->corleoneTree->getRank($this->fredo));
        self::assertEquals( 2, $this->corleoneTree->getRank($this->mike));
        self::assertEquals( 2, $this->corleoneTree->getRank($this->sonny));
        self::assertEquals( 3, $this->corleoneTree->getRank($this->clem));
        self::assertEquals( 4, $this->corleoneTree->getRank($this->frankie));
        self::assertEquals( 4, $this->corleoneTree->getRank($this->carlo));
    }

    public function testCountSubordinatesWithThreshold() : void
    {

        self::assertEquals(6, $this->corleoneTree->countSubordinatesWithThreshold($this->vito));
        self::assertEquals(3, $this->corleoneTree->countSubordinatesWithThreshold($this->mike));
        self::assertEquals(0, $this->corleoneTree->countSubordinatesWithThreshold($this->fredo));
        self::assertEquals(0, $this->corleoneTree->countSubordinatesWithThreshold($this->sonny));
        self::assertEquals(2, $this->corleoneTree->countSubordinatesWithThreshold($this->clem));
        self::assertEquals(0, $this->corleoneTree->countSubordinatesWithThreshold($this->frankie));
        self::assertEquals(0, $this->corleoneTree->countSubordinatesWithThreshold($this->carlo));

        $this->recruitFiftySubordinatesForVito();

        self::assertEquals(56, $this->corleoneTree->countSubordinatesWithThreshold($this->vito));
        self::assertEquals(6, $this->corleoneTree->countSubordinatesWithThreshold($this->vito, 6));
        self::assertEquals(50, $this->corleoneTree->countSubordinatesWithThreshold($this->vito, 50));
    }

    public function testCountSubordinatesWithWrongThreshold() : void
    {
        $this->expectException(\DomainException::class);
        $this->corleoneTree->countSubordinatesWithThreshold($this->vito, -1);

    }

    public function testContains() : void
    {
        assertTrue($this->corleoneTree->contains($this->vito));

        $newMobster = new Mobster("Luca", "Brasi", "", new \DateTime('1894-02-01'));

        self::assertFalse( $this->corleoneTree->contains($newMobster) );
    }

    public function testCount(): void
    {
        self::assertEquals(7, $this->corleoneTree->countMobsters());
        $this->recruitFiftySubordinatesForVito();
        self::assertEquals(57, $this->corleoneTree->countMobsters());
    }

    public function testRemoveMobsters() : void
    {
        self::assertCount(3, $this->corleoneTree->getDirectSubordinates($this->vito));
        assertEquals(7, $this->corleoneTree->countMobsters());

        assertTrue($this->corleoneTree->contains($this->mike));
        assertTrue($this->corleoneTree->contains($this->clem));
        assertTrue($this->corleoneTree->contains($this->frankie));
        assertTrue($this->corleoneTree->contains($this->carlo));

        $this->corleoneTree->removeMobster($this->mike, MafiaTree::REMOVE_MOBSTER_WITH_SUBORDINATES);

        self::assertCount(2, $this->corleoneTree->getDirectSubordinates($this->vito));
        assertEquals(3, $this->corleoneTree->countMobsters());

        self::assertFalse($this->corleoneTree->contains($this->mike));
        self::assertFalse($this->corleoneTree->contains($this->clem));
        self::assertFalse($this->corleoneTree->contains($this->frankie));
        self::assertFalse($this->corleoneTree->contains($this->carlo));
    }

    public function testGetDirectSubordinates() : void
    {
        assertCount(3, $this->corleoneTree->getDirectSubordinates($this->vito));
        assertCount(1, $this->corleoneTree->getDirectSubordinates($this->mike));
        assertCount(0, $this->corleoneTree->getDirectSubordinates($this->fredo));
        assertCount(0, $this->corleoneTree->getDirectSubordinates($this->sonny));
        assertCount(2, $this->corleoneTree->getDirectSubordinates($this->clem));
        assertCount(0, $this->corleoneTree->getDirectSubordinates($this->frankie));
        assertCount(0, $this->corleoneTree->getDirectSubordinates($this->carlo));
    }

    public function testGetSubordinates() : void
    {
        assertCount(6, $this->corleoneTree->getSubordinates($this->vito));
        assertCount(3, $this->corleoneTree->getSubordinates($this->mike));
        assertCount(0, $this->corleoneTree->getSubordinates($this->fredo));
        assertCount(0, $this->corleoneTree->getSubordinates($this->sonny));
        assertCount(2, $this->corleoneTree->getSubordinates($this->clem));
        assertCount(0, $this->corleoneTree->getSubordinates($this->frankie));
        assertCount(0, $this->corleoneTree->getSubordinates($this->carlo));
    }

    public function testGetBossOfMobster() : void
    {
        assertEquals(Node::EMPTY_NODE, $this->corleoneTree->getBossOfMobster($this->vito));
        assertEquals($this->vito, $this->corleoneTree->getBossOfMobster($this->mike));
        assertEquals($this->vito, $this->corleoneTree->getBossOfMobster($this->fredo));
        assertEquals($this->vito, $this->corleoneTree->getBossOfMobster($this->sonny));
        assertEquals($this->mike, $this->corleoneTree->getBossOfMobster($this->clem));
        assertEquals($this->clem, $this->corleoneTree->getBossOfMobster($this->carlo));
        assertEquals($this->clem, $this->corleoneTree->getBossOfMobster($this->frankie));

    }

}
