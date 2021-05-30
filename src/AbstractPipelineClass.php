<?php

namespace StindCo\Rapido;

class AbstractPipelineClass
{
    public Request $req;
    public Response $res;

    public function handle($req, $res, $next) {

    }
}
