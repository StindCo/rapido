<?php

namespace Autres;

class UserController
{
    private $name = "Stéphane Ngoyi";
    public function main($req, $res, $next)
    {
        $res->send("
            <h1>Je suis stéphane</h1>
            <h2>Je suis stéphane</h2>
        ");
        return $next();
    }

    public function autre () {
        echo "Je suis un test";
    }
}
