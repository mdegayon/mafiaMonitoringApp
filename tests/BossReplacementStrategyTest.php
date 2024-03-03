<?php


use PHPUnit\Framework\TestCase;
use Src\Mobster;
use Src\ReplacementBossResolver;
use Src\strategy\MobsterReplacementStrategy;
use Src\tree\mafia\MafiaNode;
use Src\tree\mafia\MafiaTree;
use Src\tree\Node;

class BossReplacementStrategyTest extends TestCase
{
    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem, $frankie;
    protected MafiaNode $vitoNode, $mikeNode, $fredoNode, $sonnyNode, $carloNode, $clemNode, $frankieNode;
    protected MafiaTree $corleoneTree;

    private MobsterReplacementStrategy $strategy;

    protected function setUp(): void
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

        $this->strategy = new MobsterReplacementStrategy($this->corleoneTree);
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

    public function testCantReplace() : void
    {
        $this->expectException(DomainException::class);
        $this->strategy->replaceMobster($this->vitoNode, $this->clemNode);
    }

    public function testReplaceWithNoSubordinates() : void
    {
        self::assertContains($this->fredoNode, $this->vitoNode->getDirectSubordinates());
        self::assertEquals($this->vitoNode, $this->fredoNode->getParent());
        $this->strategy->replaceMobster($this->fredoNode, $this->sonnyNode);
        self::assertNotContains($this->fredoNode, $this->vitoNode->getDirectSubordinates());
        self::assertEquals($this->sonnyNode, $this->fredoNode->getReplacementNode());
        self::assertEquals(Node::EMPTY_NODE, $this->fredoNode->getParent());
    }

}
