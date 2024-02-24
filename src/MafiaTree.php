<?php declare(strict_types=1);

namespace Src;

use PhpParser\Node\Scalar\String_;
use Src\Mobster;

class MafiaTree {

    private array $mobsters;
    private int $lastId;

    public function __construct() {
        $this->mobsters = [];
        $this->lastId = 0;
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
    
    public function getMobster(int $id): Mobster|null {
        
        $mobster = NULL;
        
        if ( array_key_exists($id, $this->mobsters)){
            $mobster = $this->mobsters[$id];
        }

        return $mobster;
    }

    private function generateId(): int {
        return ++$this->lastId;
    }

    private function print() : string {

    }
}
