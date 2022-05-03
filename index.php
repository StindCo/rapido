<?php

use StindCo\Rapido\App;
use StindCo\Rapido\Request;
use StindCo\Rapido\Response;

require "./vendor/autoload.php";

$app = new App();

$app->get("/", function(Request $req, Response $res, callable $next) {
    $res->sendJson(["ConsumerId" => $req->query("consumerId")]);
});

$app->run();
