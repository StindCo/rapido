<?php

namespace StindCo\Rapido;

abstract class AbstractMiddleware extends AbstractPipelineClass
{
    abstract public function handle($req, $res, $next);
}
