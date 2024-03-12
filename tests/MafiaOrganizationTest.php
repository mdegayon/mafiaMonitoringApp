<?php


use Src\MafiaOrganization;
use PHPUnit\Framework\TestCase;
use Src\Mobster;
use Src\tree\mafia\MafiaNode;
use Src\tree\mafia\MafiaTree;

class MafiaOrganizationTest extends TestCase
{
    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem, $frankie;
    private MafiaOrganization $organization;

    protected function setUp(): void
    {
        $this->vito = new Mobster("Vito", "Corleone", "The Godfather", new \DateTime('1910-02-01'));

        $this->sonny = new Mobster("Santino", "Corleone", "Sonny", new \DateTime('1935-04-01'));
        $this->fredo = new Mobster("Frederico", "Corleone", "Fredo", new \DateTime('1938-01-01'));
        $this->mike = new Mobster("Michele", "Corleone", "Mike", new \DateTime('1940-06-01'));

        $this->clem = new Mobster("Peter", "Clemenza", "Fat Clemenza", new \DateTime('1918-10-01'));
        $this->frankie = new Mobster("Francesco", "Pentangeli", "Frankie 5 Angels", new \DateTime('1920-02-01'));

        $this->carlo = new Mobster("Carlo", "Rizzi", "", new \DateTime('1930-09-01'));

        $this->organization = new MafiaOrganization($this->vito);
    }

    private function createCorleoneTree() : void
    {
        $this->organization->addMobster($this->sonny, $this->vito);
        $this->organization->addMobster($this->fredo, $this->vito);
        $this->organization->addMobster($this->mike, $this->vito);

            $this->organization->addMobster($this->frankie, $this->mike);
            $this->organization->addMobster($this->clem, $this->mike);

                $this->organization->addMobster($this->carlo, $this->clem);
    }

    public function testOrgCreation() : void
    {
        $this->createCorleoneTree();
        self::assertEquals(7, $this->organization->countMobstersInOrganization());
    }

    public function testRanks() : void
    {
        $this->createCorleoneTree();
        self::assertEquals(MafiaOrganization::FIRST_RANKS_HIGHER, $this->organization->compareMobsterRanks($this->vito, $this->mike));
        self::assertEquals(MafiaOrganization::FIRST_RANKS_HIGHER, $this->organization->compareMobsterRanks($this->vito, $this->sonny));
        self::assertEquals(MafiaOrganization::FIRST_RANKS_HIGHER, $this->organization->compareMobsterRanks($this->vito, $this->fredo));

        self::assertEquals(MafiaOrganization::SECOND_RANKS_HIGHER, $this->organization->compareMobsterRanks($this->mike, $this->vito));
        self::assertEquals(MafiaOrganization::SECOND_RANKS_HIGHER, $this->organization->compareMobsterRanks($this->sonny, $this->vito));
        self::assertEquals(MafiaOrganization::SECOND_RANKS_HIGHER, $this->organization->compareMobsterRanks($this->fredo, $this->vito));

        self::assertEquals(MafiaOrganization::RANK_EQUAL, $this->organization->compareMobsterRanks($this->sonny, $this->mike));
        self::assertEquals(MafiaOrganization::RANK_EQUAL, $this->organization->compareMobsterRanks($this->fredo, $this->mike));
        self::assertEquals(MafiaOrganization::RANK_EQUAL, $this->organization->compareMobsterRanks($this->fredo, $this->sonny));

        self::assertEquals(MafiaOrganization::FIRST_RANKS_HIGHER, $this->organization->compareMobsterRanks($this->mike, $this->clem));
        self::assertEquals(MafiaOrganization::FIRST_RANKS_HIGHER, $this->organization->compareMobsterRanks($this->mike, $this->frankie));
        self::assertEquals(MafiaOrganization::RANK_EQUAL, $this->organization->compareMobsterRanks($this->frankie, $this->clem));
        self::assertEquals(MafiaOrganization::FIRST_RANKS_HIGHER, $this->organization->compareMobsterRanks($this->clem, $this->carlo));
    }
    private function addNSubordinatesToMobster(Mobster $mobster, int $subordinatesAmount) : void
    {
        for ($i = 0; $i < $subordinatesAmount; $i++){
            $randMobster = new Mobster(
        "firstName_" . rand(0, 10000),
        "lastName_" . rand(0, 10000),
        "nickname_" . rand(0, 10000),
                new DateTime()
            );
            $this->organization->addMobster($randMobster, $mobster);
        }

    }

    public function testSpecialSurveillance() : void
    {
        $this->createCorleoneTree();

        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->vito));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->mike));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->fredo));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->sonny));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->clem));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->frankie));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->carlo));

        $this->addNSubordinatesToMobster($this->frankie, 49);

        self::assertTrue($this->organization->shouldPutUnderSpecialSurveillance($this->vito));
        self::assertTrue($this->organization->shouldPutUnderSpecialSurveillance($this->mike));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->fredo));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->sonny));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->clem));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->frankie));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->carlo));

        $this->addNSubordinatesToMobster($this->carlo, 49);

        self::assertTrue($this->organization->shouldPutUnderSpecialSurveillance($this->vito));
        self::assertTrue($this->organization->shouldPutUnderSpecialSurveillance($this->mike));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->fredo));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->sonny));
        self::assertTrue($this->organization->shouldPutUnderSpecialSurveillance($this->clem));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->frankie));
        self::assertFalse($this->organization->shouldPutUnderSpecialSurveillance($this->carlo));
    }

}

