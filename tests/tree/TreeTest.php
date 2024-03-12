<?php

namespace tree;

use Src\tree\Tree;
use Src\tree\Node;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class TreeTest extends TestCase
{
    private Tree $tree;

    private $donVito, $mike, $fredo, $sonny, $clemenza, $frankieFiveAngels;
    private Node $vitoNode, $mikeNode, $fredoNode, $sonnyNode, $clemNode, $frankieNode;

    protected function setUp() : void
    {
        $this->donVito = 'Don Vito';
        $this->tree = new Tree($this->donVito);

        $this->vitoNode = $this->tree->getRoot();
    }

    private function buildCorleonesiTree() : void
    {
        $this->mike = 'Mike';
        $this->fredo = 'Fredo';
        $this->clemenza = 'Clemenza';
        $this->frankieFiveAngels = 'Frankie';

        $this->mikeNode = $this->tree->add($this->mike, $this->vitoNode);
        $this->fredoNode = $this->tree->add($this->fredo, $this->vitoNode);
        $this->sonnyNode = $this->tree->add($this->sonny, $this->vitoNode);

        $this->clemNode = $this->tree->add($this->clemenza, $this->mikeNode);
        $this->frankieNode = $this->tree->add($this->frankieFiveAngels, $this->clemNode);
    }

    public function testAdd() : void
    {
        self::assertCount(0, $this->vitoNode->getChildren());
        $this->mikeNode = $this->tree->add($this->mike, $this->vitoNode);
        self::assertCount(1, $this->vitoNode->getChildren());
        self::assertEquals($this->vitoNode, $this->mikeNode->getParent());
        self::assertEquals($this->mikeNode, $this->vitoNode->find($this->mike));
        self::assertEquals($this->mikeNode, $this->vitoNode->findNode($this->mikeNode));
    }

    public function testGetRoot() : void
    {
        self::assertEquals($this->vitoNode, $this->tree->getRoot());
    }

    public function testRemove() : void
    {
        $this->buildCorleonesiTree();

        self::assertFalse($this->vitoNode->removeChild('?'));
        self::assertCount(3, $this->vitoNode->getChildren());
        self::assertEquals($this->fredoNode, $this->vitoNode->find($this->fredo));
        self::assertTrue($this->vitoNode->removeChild($this->fredo));
        self::assertCount(2, $this->vitoNode->getChildren());
        self::assertNull($this->fredoNode->getParent());
        self::assertNull($this->vitoNode->find($this->fredo));
    }

    public function testRemoveNode() : void
    {
        $this->buildCorleonesiTree();

        self::assertFalse($this->vitoNode->removeChildNode(new Node('?')));
        self::assertCount(3, $this->vitoNode->getChildren());
        self::assertEquals($this->fredoNode, $this->vitoNode->find($this->fredo));
        self::assertTrue($this->vitoNode->removeChildNode($this->fredoNode));
        self::assertCount(2, $this->vitoNode->getChildren());
        self::assertNull($this->fredoNode->getParent());
        self::assertNull($this->vitoNode->find($this->fredo));
    }

    public function testMoveNode() : void
    {
        $this->buildCorleonesiTree();

        self::assertCount(3, $this->vitoNode->getChildren());
        self::assertCount(1, $this->mikeNode->getChildren());
        self::assertEquals($this->mikeNode, $this->clemNode->getParent());
        self::assertTrue( $this->tree->moveNode($this->clemNode, $this->vitoNode) );
        self::assertCount(4, $this->vitoNode->getChildren());
        self::assertCount(0, $this->mikeNode->getChildren());
        self::assertNull($this->mikeNode->find($this->clemenza));
        self::assertNull($this->mikeNode->findNode($this->clemNode));
        self::assertEquals($this->clemNode, $this->vitoNode->find($this->clemenza));
        self::assertEquals($this->clemNode, $this->vitoNode->findNode($this->clemNode));
        self::assertEquals($this->vitoNode, $this->clemNode->getParent());
    }

    public function testMoveRoot() : void
    {
        $this->buildCorleonesiTree();

        self::expectException(\DomainException::class);
        $this->tree->moveNode($this->vitoNode, $this->sonnyNode);
    }

    public function testTraverseFromRoot()
    {
        self::expectNotToPerformAssertions();
    }

    public function testTraverse()
    {
        self::expectNotToPerformAssertions();
    }
}
