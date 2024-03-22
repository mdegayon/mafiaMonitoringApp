<?php declare(strict_types=1);

namespace Src;

use Src\MafiaState;

class Mobster{

    private MafiaState $state;
    private String $firstName;
    private String $lastName;
    private String $nickname;

    private bool $hasReplacementBoss;

    private \DateTime $recruitmentDate;

    public function __construct(String $firstName, String $lastName, String $nickname, \DateTime $recruitment_date)
    {
        $this->state = MafiaState::Active;
        $this->hasReplacementBoss = false;

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->nickname = $nickname;
        $this->recruitmentDate = $recruitment_date;
    }

    public function HasReplacementBoss(): bool
    {
        return $this->hasReplacementBoss;
    }

    public function setHasReplacementBoss(bool $hasReplacementBoss): void
    {
        $this->hasReplacementBoss = $hasReplacementBoss;
    }

    public function getRecruitmentDate() : \DateTime
    {
        return $this->recruitmentDate;
    }

    public function getState() : MafiaState{
        return $this->state;
    }

    public function setState(MafiaState $state) : void{
        $this->state = $state;
    }

    public function __toString(): string
    {
        if (!empty($this->nickname)) {
            $mobsterAsString = sprintf(
                "%s \"%s\" %s",
                $this->firstName,
                $this->nickname,
                $this->lastName
            );
        } else {
            $mobsterAsString = sprintf(
                "%s %s",
                $this->firstName,
                $this->lastName
            );
        }

        return $mobsterAsString;
    }

    public function getKey() : string {
        return sprintf(
            "%s,%s.AKA=%s",
            $this->lastName,
            $this->firstName,
            $this->nickname
        );
    }
    




}
