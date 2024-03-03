<?php declare(strict_types=1);

namespace Src;

use Src\strategy\ActiveBossFinderStrategy;
use Src\strategy\MobsterReplacementStrategy;
use Src\strategy\PositionRecoveryStrategy;
use Src\tree\mafia\MafiaNode;
use Src\tree\mafia\MafiaTree;
use Src\tree\Node;

class MafiaOrganization {

    private array $mobsterNodes;
    private MafiaTree $mafiaTree;

    private ReplacementBossResolver $resolver;
    private MobsterReplacementStrategy $replacementStrategy;
    private PositionRecoveryStrategy $positionRecoveryStrategy;

    public function __construct(Mobster $theDon)
    {
        $theDonNode = new MafiaNode($theDon);

        $this->mafiaTree = new MafiaTree(new MafiaNode($theDonNode));
        $this->addNode($theDon->getKey(), $theDonNode);

        $this->resolver = new ReplacementBossResolver();
        $this->replacementStrategy = new MobsterReplacementStrategy($this->mafiaTree);
        $this->positionRecoveryStrategy = new PositionRecoveryStrategy(new ActiveBossFinderStrategy($this->mafiaTree));
    }

    private function addNode(string $key, MafiaNode $node) : void
    {
        $this->mobsterNodes[$key] = $node;
    }

    public function addMobster(Mobster $mobster, Mobster $boss) : void
    {
        $bossNode = $this->findNode($boss->getKey());
        $mobsterNode = new MafiaNode($mobster);

        $this->mafiaTree->addNode($mobsterNode, $bossNode);
        $this->addNode($mobster->getKey(), $mobsterNode);
    }

    public function countMobstersInOrganization() : int
    {
        return sizeof($this->mobsterNodes);
    }

    public function shouldPutUnderSpecialSurveillance(Mobster $mobster) : bool
    {
        $suspectMobsterNode = $this->findNode($mobster->getKey());

        return $this->mafiaTree->shouldPutNodeUnderSpecialSurveillance($suspectMobsterNode);
    }

    public function compareMobsterRanks(Mobster $first, Mobster $second) : int
    {
        $firstNode = $this->findNode($first->getKey());
        $secondNode = $this->findNode($second->getKey());

        return $this->mafiaTree->compareNodeRanks($firstNode, $secondNode);
    }

    private function findNode(string $key) : MafiaNode
    {
        if (!array_key_exists($key, $this->mobsterNodes)){
            throw new \DomainException("Mobster with key=$key is missing from our records");
        }
        return $this->mobsterNodes[$key];
    }

    public function sendToPrison(Mobster $imprisonedMobster) : void
    {
        $imprisonedMobsterNode = $this->findNode($imprisonedMobster->getKey());
        $imprisonedMobster->setState(MafiaState::Imprisoned);
        // TODO Discuss with SYX (Should findReplacementBossFor throw the exception itself already ?)
        $replacementBoss = $this->resolver->findReplacementBossFor($imprisonedMobsterNode);
        if ($replacementBoss === Node::EMPTY_NODE){
            throw new \DomainException("Can't find replacement boss for Mobster. Can't send him to prison. Mobster: $imprisonedMobster");
        }
        $this->replacementStrategy->replaceMobster($imprisonedMobsterNode, $replacementBoss);
    }

    public function releaseFromPrison (Mobster $releasedMobster) : void
    {
        $releasedMobsterNode = $this->findNode($releasedMobster->getKey());
        $releasedMobster->setState(MafiaState::Active);

        $this->positionRecoveryStrategy->recoverPositionOf($releasedMobsterNode);
    }

/*

    public function print() : void {
        $this->mafiaTree->traverseFromRoot();
    }

        private int $lastId;

        public function __construct(Mobster $theBoss) {

            $this->lastId = -1;

            $this->addNode($theBoss);
        }

        private function addNode(Mobster $member) : Node {
            $id = $this->generateId();
            $mobsterNode =  new Node($id, $member);
            $this->mobsters[$id] = $mobsterNode;

            return $mobsterNode;
        }

        public function addMemberToTheFamily(Mobster $member, Mobster|null $boss=null) : int
        {
            $mobsterNode = $this->addNode($member);

            if ($boss){
                $bossNode = $this->mobsters[$boss->getId()];
                $bossNode->addChild($mobsterNode);
                $mobsterNode->setParent($bossNode);
            }

            return $member->getId();
        }

        private function generateId(): int {
            return ++$this->lastId;
        }

        public function print() : void {
            $this->mafiaTree->traverseFromRoot();
        }

        public function getMobster(int $id): Mobster|null {

            $mobster = NULL;

            if ( array_key_exists($id, $this->mobsters)){
                $mobster = $this->mobsters[$id];
            }

            return $mobster;
        }

        public function addMobster(String $firstName, String $lastName, String $nickname, int $bossId): int {
            $id = $this->generateId();

            $boss = $this->getMobster($bossId);

            $mobster = new Mobster(
                $id,
                $firstName,
                $lastName,
                $nickname,
                $boss,
                []
            );
            $this->mobsters[$id] = $mobster;

            if($boss !== null){
                $this->mobsters[$bossId]->addSubordinate($mobster);
            }

            return $id;
        }
    */
}
