<?php

namespace StindCo\Rapido;

class PipeLine
{
    private $continue = false;
    public function __construct(Request $req, Response $res)
    {
        $this->req = $req;
        $this->res = $res;

        $this->next();
    }
    private function next()
    {
        $this->continue = true;
    }
    public function pipe($classe)
    {
        $next = function () {
            return true;
        };
        if ($this->continue == true) {
            if (is_callable($classe)) {
                $this->continue = $classe($this->req, $this->res, $next);
            } elseif (is_string($classe)) {
                $this->continue = (new $classe)
                    ->handle($this->req, $this->res, $next);
            } else {
                $this->continue = ($classe)->handle($this->req, $this->res, $next);
            }
        }

        return $this;
    }

    public function handle ($req, $res, $next) {
        if($this->continue == true) return $next();
    }
}
