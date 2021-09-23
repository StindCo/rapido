<?php
use StindCo\Rapido\App;
use StindCo\Rapido\Request;
use StindCo\Rapido\Response;

require "./vendor/autoload.php";
error_reporting(0);
 $app = new App();

// Créer une route c'est simple 

$app->get("/", function (Request $req, Response $res, Callable $next) {
    // Il va afficher "je suis stéphane" si l'url est de "http://nomdedomaine.com/
    echo "Je suis stéphane ";
});

//on va créer une autre route

$app->post("/salutation", function(Request $req, Response $res, Callable $next){
    /**
     * On va créer une mini api 
     * on récupère le nom dans les params 
     */
/*     $nom = $req->get_getDatas()->nom;
    if(is_null($nom)) {
        return $res->send("Tu n'as pas mis ton nom");
    }
    elseif($nom == "voldie") {
        $res->send("Salut gros !");
    }
    $res->send("Bonjour {$nom}");
    */
    $method = $req->get("DOCUMENT_ROOT"); // tu peux recupérer automatiquement en Poo tous les élements du $_SERVER
    // Pas mal hein ...
    $res->send();
});
/**
 * Il existe aussi
 * $req->get_postDatas() pour récuperer les données envoyés via un formulaire, ou par le méthode Post
 * $req->get_putDatas() Pour les données envoyés via la méthode PUT
 */




// Ensuite il faut lancer l'application
$app->post("/", function(Request $req, $res, $next) {
    
    return $res->sendJson(["data"=> "Tu es ". $req->inputData()->username . " et ton password est " . $req->inputData()->password]);
});

$app->run();