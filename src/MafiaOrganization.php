<?php declare(strict_types=1);

namespace Src;

use Src\strategy\ReplacementFinderStrategy;
use Src\strategy\ReplacingMobsterStrategy;
use Src\strategy\PositionRecoveryStrategy;
use Src\tree\mafia\MafiaTree;
use Src\tree\Node;

class MafiaOrganization {

    private MafiaTree $mafiaTree;

    private ReplacementFinderStrategy $replacementFinder;
    private ReplacingMobsterStrategy $replacementStrategy;
    private PositionRecoveryStrategy $positionRecoveryStrategy;

    public function __construct(Mobster $theDon)
    {
        $this->mafiaTree = new MafiaTree($theDon);

        $this->replacementFinder = new ReplacementFinderStrategy($this->mafiaTree);
        $this->replacementStrategy = new ReplacingMobsterStrategy($this->mafiaTree);
        $this->positionRecoveryStrategy = new PositionRecoveryStrategy($this->mafiaTree);
    }

    public function addMobster(Mobster $mobster, Mobster $boss) : void
    {
        $this->mafiaTree->addMobster($mobster, $boss);
    }

    public function countMobstersInOrganization() : int
    {
        return $this->mafiaTree->countMobsters();
    }

    public function getBoss(Mobster $mobster) : Mobster
    {
        return $this->mafiaTree->getBossOfMobster($mobster);
    }

    public function getDirectSubordinateS(Mobster $mobster) : array
    {
        return $this->mafiaTree->getDirectSubordinates($mobster);
    }

    public function replaceImprisonedMobster(Mobster $imprisonedMobster) : Mobster
    {
        // TODO Discuss with SYX (Should findReplacementBossFor throw the exception itself already ?)
        // TODO Refactor NODE_EMPTY... Organization should not know about nodes
        $replacement = $this->replacementFinder->findReplacementFor($imprisonedMobster);
        if ($replacement === Node::EMPTY_NODE){
            throw new \DomainException(
                "Can't find replacement boss for Mobster. Can't send him to prison. Mobster: $imprisonedMobster"
            );
        }

        $this->replacementStrategy->replaceMobster($imprisonedMobster, $replacement);

        return $replacement;
    }

    public function recoverMobsterPosition (Mobster $releasedMobster, MafiaPosition $position) : void
    {
        $this->positionRecoveryStrategy->recoverPositionOf($releasedMobster, $position);
    }

    public function mobsterBelongsToOrganization(Mobster $mobster) : bool
    {
        return $this->mafiaTree->contains($mobster);
    }

    public function countSubordinatesWithThreshold($mobster, $threshold = MafiaTree::NO_THRESHOLD) : int
    {
        return $this->mafiaTree->countSubordinatesWithThreshold($mobster, $threshold);
    }

    public function getRank(Mobster $mobster) : int
    {
        return $this->mafiaTree->getRank($mobster);
    }

    public function print() : void
    {
        $this->mafiaTree->print();
    }
}
