<?php

namespace tree;

use Src\tree\Node;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNull;

class NodeTest extends TestCase
{

    private Node $parentNode;
    private Node $childA, $childB, $childC;
    private Node $grandChildD, $grandChildE, $grandChildF;

    protected function setUp(): void
    {
        $this->parentNode = new Node('p');
        $this->childA = new Node('a');
        $this->childB = new Node('b');
        $this->childC = new Node('c');
        $this->grandChildD= new Node('d');
        $this->grandChildE = new Node('e');
        $this->grandChildF = new Node('f');

        $this->parentNode->addChildNode($this->childA);
        $this->parentNode->addChildNode($this->childB);
        $this->parentNode->addChildNode($this->childC);

        $this->childA->addChildNode($this->grandChildD);
        $this->childA->addChildNode($this->grandChildE);
        $this->childA->addChildNode($this->grandChildF);
    }

    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function testEquals()
    {
        $nodeWithA = new Node('a');
        $nodeWithAToo = new Node('a');
        $nodeWithB = new Node('b');

        self::assertTrue($nodeWithA->equals($nodeWithAToo));
        self::assertFalse($nodeWithA->equals($nodeWithB));
        self::assertFalse($nodeWithB->equals($nodeWithAToo));

    }

    public function testSetParent()
    {
        $nodeWithParent = new Node('a');
        $nodeWithNoParent = new Node('b');
        $parentNode = new Node('p');
        $nodeWithParent->setParent($parentNode);
        self::assertEquals($parentNode, $nodeWithParent->getParent());
        self::assertEquals(Node::EMPTY_NODE, $nodeWithNoParent->getParent());
    }

    public function testAddAndFindChild()
    {
        $parentNode = new Node('p');
        $childNode = new Node('c');

        $parentNode->addChildNode($childNode);
        self::assertTrue($childNode->hasParent());
        self::assertEquals($parentNode, $childNode->getParent());
        self::assertEquals($childNode, $parentNode->find('c'));
        self::assertEquals(Node::EMPTY_NODE, $parentNode->find('?'));
        self::assertEquals(1, sizeof($parentNode->getChildren()) );
    }

    public function testFind()
    {
        self::assertEquals($this->childA, $this->parentNode->find('a'));
        self::assertEquals($this->childB, $this->parentNode->find('b'));
        self::assertEquals($this->childC, $this->parentNode->find('c'));

        self::assertEquals($this->grandChildD, $this->childA->find('d'));
        self::assertEquals($this->grandChildE, $this->childA->find('e'));
        self::assertEquals($this->grandChildF, $this->childA->find('f'));

        self::assertNull($this->parentNode->find('d'));
        self::assertNull($this->parentNode->find('e'));
        self::assertNull($this->parentNode->find('f'));

        self::assertNull($this->parentNode->find('?'));
    }

    public function testFindNode()
    {
        self::assertEquals($this->childA, $this->parentNode->findNode($this->childA));
        self::assertEquals($this->childB, $this->parentNode->findNode($this->childB));
        self::assertEquals($this->childC, $this->parentNode->findNode($this->childC));

        self::assertEquals($this->grandChildD, $this->childA->findNode($this->grandChildD));
        self::assertEquals($this->grandChildE, $this->childA->findNode($this->grandChildE));
        self::assertEquals($this->grandChildF, $this->childA->findNode($this->grandChildF));

        self::assertNull($this->parentNode->findNode($this->grandChildD));
        self::assertNull($this->parentNode->findNode($this->grandChildD));
        self::assertNull($this->parentNode->findNode($this->grandChildD));

        self::assertNull($this->parentNode->findNode(new Node('?')));
    }

    public function testRemoveChild()
    {
        self::assertEquals(3, sizeof($this->parentNode->getChildren()));
        self::assertFalse($this->parentNode->removeChild('?'));
        self::assertTrue($this->parentNode->removeChild('a'));
        self::assertEquals(2, sizeof($this->parentNode->getChildren()));
        self::assertNull($this->parentNode->find('a'));
        self::assertNull($this->childA->getParent());
        self::assertFalse($this->childA->hasParent());
        self::assertEquals($this->parentNode, $this->childB->getParent());
        self::assertEquals($this->parentNode, $this->childC->getParent());
    }

    public function testGetParent()
    {
        self::assertNull($this->parentNode->getParent());
        self::assertEquals($this->parentNode, $this->childA->getParent());
        self::assertEquals($this->parentNode, $this->childB->getParent());
        self::assertEquals($this->parentNode, $this->childC->getParent());
        self::assertEquals($this->childA, $this->grandChildD->getParent());
        self::assertEquals($this->childA, $this->grandChildE->getParent());
        self::assertEquals($this->childA, $this->grandChildF->getParent());
    }

    public function testGetData()
    {
        assertEquals('p', $this->parentNode->getData());
        assertEquals('a', $this->childA->getData());
        assertEquals('b', $this->childB->getData());
        assertEquals('c', $this->childC->getData());
        assertEquals('d', $this->grandChildD->getData());
        assertEquals('e', $this->grandChildE->getData());
        assertEquals('f', $this->grandChildF->getData());
    }

    public function testRemoveChildNode()
    {
        self::assertEquals(3, sizeof($this->parentNode->getChildren()));
        self::assertFalse($this->parentNode->removeChildNode(new Node('?')));
        self::assertTrue($this->parentNode->removeChildNode($this->childA));
        self::assertEquals(2, sizeof($this->parentNode->getChildren()));
        self::assertNull($this->parentNode->findNode($this->childA));
        self::assertNull($this->childA->getParent());
        self::assertFalse($this->childA->hasParent());
        self::assertEquals($this->parentNode, $this->childB->getParent());
        self::assertEquals($this->parentNode, $this->childC->getParent());
    }

    public function testSetData()
    {
        $this->parentNode->setData('x');
        self::assertEquals('x', $this->parentNode->getData());
    }

    public function testGetChildren()
    {
        $parentNodeChildren = $this->parentNode->getChildren();
        assertEquals(3, sizeof($parentNodeChildren));
        assertEquals($this->childA, $parentNodeChildren[0]);
        assertEquals($this->childB, $parentNodeChildren[1]);
        assertEquals($this->childC, $parentNodeChildren[2]);

        $childAChildren = $this->childA->getChildren();
        self::assertEquals($this->grandChildD, $childAChildren[0]);
        self::assertEquals($this->grandChildE, $childAChildren[1]);
        self::assertEquals($this->grandChildF, $childAChildren[2]);
    }

}
