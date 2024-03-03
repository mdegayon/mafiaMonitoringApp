<?php

namespace tree;

use Src\Mobster;
use Src\tree\Node;
use Src\tree\Tree;
use PHPUnit\Framework\TestCase;

class TreeTest extends TestCase
{

    private Tree $tree;

    protected function setUp() : void
    {
        $donVito = new Mobster(
            "Vito",
            "Corleone",
            "Don Vito",
            new \DateTime()
        );
        $mike = new Mobster(
            "Michele",
            "Corleone",
            "Mike",
            new \DateTime()
        );
        $fredo = new Mobster(
            "Frederico",
            "Corleone",
            "Fredo",
            new \DateTime()
        );
        $clemenza = new Mobster(
            "Peter",
            "Clemenza",
            "Clem",
            new \DateTime()
        );

        $rootNode = new Node(0, $donVito);
        $this->tree = new Tree($rootNode);

        $mikeNode = new Node(0, $mike);
        $fredoNode = new Node(0, $fredo);
        $rootNode->addChild($mikeNode);
        $rootNode->addChild($fredoNode);

        $clemNode = new Node(0, $clemenza);
        $mikeNode->addChild($clemNode);
    }

    public function testTraverseFromRoot()
    {
        $this->expectNotToPerformAssertions();
    }

    public function test__construct()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testTraverse()
    {
        $this->expectNotToPerformAssertions();
    }
}
