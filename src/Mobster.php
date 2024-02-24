<?php declare(strict_types=1);

namespace Src;

use Src\MafiaState;
use Src\tree\NodeData;

class Mobster implements  NodeData {
    const NULL_ID = 0;

    private int $id;
    private MafiaState $state;
    private String $firstName;
    private String $lastName;
    private String $nickname;
    private Mobster|null $boss;
    private Array $subordinates;

    public function __construct(int $id, String $firstName, String $lastName, String $nickname, Mobster|null $boss, Array $subordinates) {
        $this->id = $id;
        $this->state = MafiaState::Active;
        
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->nickname = $nickname;
        $this->boss = $boss;
                
        $this->subordinates = $subordinates;
    }

    public function addSubordinate(Mobster $subordinate): void { 
        
        if ($subordinate->boss !== null){
            $subordinate->boss->removeSubordinate($subordinate);
        }
        
        $subordinate->boss = $this;
        $this->subordinates[$subordinate->id] = $subordinate;
    }
    
    public function removeSubordinate(Mobster $subordinate) : void {        
        $subordinate->boss = null;
        unset( $this->subordinates[$subordinate->id] );
    }
    
    public function toString(): String {
        if (!empty($this->nickname)) {
            $mobsterAsString = sprintf(
                "[%d] %s \"%s\" %s",
                $this->id,
                $this->firstName,
                $this->nickname,
                $this->lastName
            );
        } else {
            $mobsterAsString = sprintf(
                "[%d] %s %s",
                $this->id,
                $this->firstName,
                $this->lastName
            );
        }

        return $mobsterAsString;
    }
    
    public function countSubordinates(): int { 
        $subordinatesCount = 0;

        foreach ($this->subordinates as $subordinate) {
            $subordinatesCount += 1 + $subordinate->countSubordinates();                
        }
        
        return $subordinatesCount;
    }
    
    public function countDirectSubordinates() : int {        
        return sizeof($this->subordinates);
    }

    public function getState() : MafiaState{
        return $this->state;
    }
    
    public function setState(MafiaState $state) : void{
        $this->state = $state;
    }
    
    public function getBoss() : Mobster|null {
        return $this->boss;
    }
    
    public function getOldestSubordinate() : Mobster|null{
        
        $oldestSubordinate = null;
        foreach ($this->subordinates as $subordinate) {

            if ($oldestSubordinate == null || $subordinate->isOlder($oldestSubordinate)){
                $oldestSubordinate = $subordinate;
            }

            if ($subordinate->getState() != MafiaState::Active){
                continue;
            }
            
        }
        
        return $oldestSubordinate;
    }
    
    public function isOlder(Mobster $other) : bool
    {
        return $this->id < $other->id;
    }
    
    public function getSubordinates () : array {
        return $this->subordinates;
    }
    
    public function changeBossOfSubordinates(Mobster $newBoss) : void {

        foreach ($this->subordinates as $subordinate) {
            $this->changeBossOfSubordinate($newBoss, $subordinate);
        }
    }
    
    public function getId() : int{
        return $this->id;
    }
    
    private function changeBossOfSubordinate(Mobster $newBoss, Mobster $subordinate) : void {

        $this->removeSubordinate($subordinate);
        if ($newBoss){
            $newBoss->addSubordinate($subordinate);            
        }
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
