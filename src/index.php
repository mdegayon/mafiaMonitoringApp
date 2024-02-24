<?php
    declare(strict_types=1);
    require '../vendor/autoload.php';

    use Src\MafiaTree;
    use Src\Mobster;
    use Src\FBITrackingMafiaApp;

    

    exit;

    $mobsters = new MafiaTree();

    $capoId = $mobsters->addMobster(
        "Vito",
        "Corleone",
        "Don Vito",
        Mobster::NULL_ID,
    );
    $mikeId = $mobsters->addMobster(
        "Michael",
        "Corleone",
        "Mike",
        $capoId,
    );    
    $fredoId = $mobsters->addMobster(
        "Frederico",
        "Corleone",
        "Fredo",
        $capoId,
    );
    $johnId = $mobsters->addMobster(
        "John",
        "Doe",
        "",
        $mikeId,
    );    
    
    $capo = $mobsters->getMobster($capoId);
    $mike = $mobsters->getMobster($mikeId);
    $fredo = $mobsters->getMobster($fredoId);
    $john = $mobsters->getMobster($johnId);

    echo $capo->toString(). "\n";
    echo $mike->toString(). "\n";
    echo $fredo->toString(). "\n";
    echo $john->toString(). "\n";

    echo $capo->countSubordinates() ."\n";
    echo $mike->countSubordinates() ."\n";
    echo $fredo->countSubordinates() ."\n";
    echo $john->countSubordinates() ."\n";
    
    $app = new FBITrackingMafiaApp();
    
    $app->sendToPrison($mike);
    
    echo "\n\n\n";    
    echo $capo->countSubordinates() ."\n";
    echo $mike->countSubordinates() ."\n";
    echo $fredo->countSubordinates() ."\n";    
    echo $john->countSubordinates() ."\n";    
    
    $app->releaseFromPrison($mike);
    
    echo "\n\n\n";    
    echo $capo->countSubordinates() ."\n";
    echo $mike->countSubordinates() ."\n";
    echo $fredo->countSubordinates() ."\n";    
    echo $john->countSubordinates() ."\n";