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
// un routeur
$app->get("/", function(Request $req, Response $res, callable $next) {
    return $res->send("Hello world");
});

$app->get("/user", function (Request $req,Response $res, $next) {
    $data = [
        'stephane' => [
            'sexe' => "M",
            'nom' => "Ngoyi",
            'Classe' => "Seconde",
            'Age' => 15,
        ]
    ];
    // on récupère la query (le paramètre)
    $name = $req->query('name');
    
    if(is_array($data[$name])) {
        return $res->status(200)->sendJson($data[$name]);
    } 
    return $res->status(404)->sendJson(['error_message' => "votre nom est inconnu"]);
});
// on lance l'application
$app->run();
