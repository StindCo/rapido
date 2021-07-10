<?php

namespace StindCo\Rapido;

abstract class AbstractPipelineClass
{
    public Request $req;
    public Response $res;

    abstract public function handle($req, $res, $next);
}
