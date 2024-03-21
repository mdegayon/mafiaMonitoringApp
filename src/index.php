<?php declare(strict_types=1);

    require '../vendor/autoload.php';

    use Src\Mobster;
    use Src\MafiaMonitoringApp;

    $donVito = new Mobster("Vito", "Corleone", "The Godfather", new \DateTime('1910-02-01'));
    $sonny = new Mobster("Santino", "Corleone", "Sonny", new \DateTime('1935-04-01'));
    $fredo = new Mobster("Frederico", "Corleone", "Fredo", new \DateTime('1938-01-01'));
    $mike = new Mobster("Michele", "Corleone", "Mike", new \DateTime('1940-06-01'));
    $clemenza = new Mobster("Peter", "Clemenza", "Fat Clemenza", new \DateTime('1918-10-01'));
    $frankie = new Mobster("Francesco", "Pentangeli", "Frankie 5 Angels", new \DateTime('1920-02-01'));
    $carlo = new Mobster("Carlo", "Rizzi", "", new \DateTime('1930-09-01'));

    $app = new MafiaMonitoringApp($donVito);

    $app->addMobster($sonny, $donVito);
    $app->addMobster($fredo, $donVito);
    $app->addMobster($mike, $donVito);

    $app->addMobster($clemenza, $mike);
    $app->addMobster($frankie, $mike);

    $app->addMobster($carlo, $clemenza);

    $app->print();

    exit;