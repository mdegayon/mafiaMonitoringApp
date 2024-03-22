<?php declare(strict_types=1);

namespace Src;

enum MafiaState{
    
    case Active;
    case Imprisoned;
//    case Retired;    You don't get to retire from La Mafia
    case Deceased;
}
