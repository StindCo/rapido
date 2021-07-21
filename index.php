<?php

use StindCo\Rapido\App;
use StindCo\Rapido\Request;
use StindCo\Rapido\Response;

require './vendor/autoload.php';

$app = new App();
/**
 * ============================================
 *      Définition des configurations
 * ============================================
 */
$app->config("route_mode", "/");

/**
 * ============================================
 *      Les middlewares
 * ============================================
 */

$app->use(function ($req, $res, $next) use ($app) {
    $app['nom'] = 'Stéphane Ngoyi';
    return $next();
});

$app->use(function ($req, $res, $next) use ($app) {
    $app['password'] = 'motdepasse';
    return $next();
});
$app->use(function ($req, $res, $next) use ($app) {
    if ($app['password'] == "motdepasse") {
        return $next();
    }
    $res->status(401)->sendJson(["error"  => "Ton mot de passe n'est pas correct "]);
});
/**
 * ============================================
 *      Le routage
 * ============================================
 */

$groupPrivate = $app->group('/home');

$groupPrivate->get('/home', function (Request $req, Response $res, $next) {
    $res->send("<h1>Bonjour bro </h1>");
});



$app->get('/', function ($req, $res, $next) use ($app) {
   
    $res->sendJson(["message" => "Bonjour bro !"]);
}, 'Cette route permet d\'avoir accès aux datas');

/**
 * ============================================
 *      Lancer l'application
 * ============================================
 */

$app->run();
