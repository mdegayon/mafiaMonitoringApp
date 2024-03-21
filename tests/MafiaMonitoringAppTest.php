<?php


use Src\MafiaMonitoringApp;
use PHPUnit\Framework\TestCase;
use Src\Mobster;

class MafiaMonitoringAppTest extends TestCase
{

    protected Mobster $vito, $mike, $fredo, $sonny, $carlo, $clem, $frankie;
    private MafiaMonitoringApp $app;

    protected function setUp(): void
    {
        $this->vito = new Mobster("Vito", "Corleone", "The Godfather", new \DateTime('1910-02-01'));

        $this->sonny = new Mobster("Santino", "Corleone", "Sonny", new \DateTime('1935-04-01'));
        $this->fredo = new Mobster("Frederico", "Corleone", "Fredo", new \DateTime('1938-01-01'));
        $this->mike = new Mobster("Michele", "Corleone", "Mike", new \DateTime('1940-06-01'));

        $this->clem = new Mobster("Peter", "Clemenza", "Fat Clemenza", new \DateTime('1918-10-01'));
        $this->frankie = new Mobster("Francesco", "Pentangeli", "Frankie 5 Angels", new \DateTime('1920-02-01'));

        $this->carlo = new Mobster("Carlo", "Rizzi", "", new \DateTime('1930-09-01'));

        $this->app = new MafiaMonitoringApp($this->vito);

        $this->createCorleoneTree();
    }

    private function createCorleoneTree() : void
    {
        $this->app->addMobster($this->sonny, $this->vito);
        $this->app->addMobster($this->fredo, $this->vito);
        $this->app->addMobster($this->mike, $this->vito);

        $this->app->addMobster($this->frankie, $this->mike);
        $this->app->addMobster($this->clem, $this->mike);

        $this->app->addMobster($this->carlo, $this->clem);
    }

    public function testCompareMobsterRanks()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testAddMobster()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testCountMobstersInOrganization()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testShouldPutUnderSpecialSurveillance()
    {
        $this->expectNotToPerformAssertions();
    }

    public function testSendToPrison()
    {
        $imprisonedMobster = $this->mike;
        $subordinates = $this->app->getSubordinates($imprisonedMobster);
        $replacement = $this->sonny;
        $imprisonedMobsterSubordinatesCount = count($subordinates);
        $replacementSubordinatesCount = count( $this->app->getSubordinates($replacement) );

        $this->app->sendToPrison($imprisonedMobster);

        self::assertEquals(6, $this->app->countMobstersInOrganization());
        self::assertTrue($this->app->isMobsterInPrison($imprisonedMobster));
        self::assertFalse($this->app->mobsterBelongsToOrganization($imprisonedMobster));
        self::assertEquals(\Src\MafiaState::Imprisoned, $imprisonedMobster->getState());
        self::assertCount(
            $imprisonedMobsterSubordinatesCount + $replacementSubordinatesCount,
            $this->app->getSubordinates($replacement)
        );

        /* @var Mobster $subordinate*/
        foreach ($subordinates as $subordinate){
            self::assertEquals($replacement, $this->app->getBoss($subordinate));
        }

        $clemSubordinates = $this->app->getSubordinates($this->clem);
        self::assertCount(1, $clemSubordinates);
        foreach ($clemSubordinates as $clemSubordinate){
            self::assertEquals($this->clem, $this->app->getBoss($clemSubordinate));
        }
    }

    public function testReleaseFromPrison()
    {
        //TODO RAKO DISCUSS: this test's requiring some other feature to be executed before...
        $imprisonedMobster = $this->mike;
        $subordinates = $this->app->getSubordinates($imprisonedMobster);
        $replacement = $this->sonny;
        $imprisonedMobsterSubordinatesCount = count($subordinates);
        $replacementSubordinatesCount = count( $this->app->getSubordinates($replacement) );

        $this->app->sendToPrison($imprisonedMobster);

        $this->app->releaseFromPrison($imprisonedMobster);

        self::assertCount($imprisonedMobsterSubordinatesCount, $this->app->getSubordinates($imprisonedMobster));
        self::assertCount($replacementSubordinatesCount, $this->app->getSubordinates($replacement));
        self::assertEquals($this->app->getSubordinates($imprisonedMobster), $subordinates);

        /* @var Mobster $subordinate*/
        foreach ($subordinates as $subordinate){
            self::assertEquals($imprisonedMobster, $this->app->getBoss($subordinate));
        }
    }

    private function imprisonMike() : void
    {
        $imprisonedMobster = $this->mike;

        $this->app->sendToPrison($this->mike);
    }

}
