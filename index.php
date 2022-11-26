<?php

use StindCo\Rapido\App;
use StindCo\Rapido\Request;
use StindCo\Rapido\Response;

// on charge Le composer autoload
require "./vendor/autoload.php";
/**
 * on crée l'application 
 * @var StindCo\Rapido\App $app 
 */
$app = new App();


$users = [
    [
        "name" => "Stéphane Ngoyi",
        "age" => 20,
        "email" => "sngoyi5@gmail.com",
    ]
];


$userManagementApp = $app->group('/users');



// on lance l'application
$app->run();
