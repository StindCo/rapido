<?php

namespace StindCo\Rapido;

use ReflectionClass;

class Route
{
    public $path;
    public $method;
    public $name;
    public $callback;

    public function __construct($path, $method, $callback)
    {
        $this->path = $path;
        $this->method = $method;

        $this->callback = $callback;
    }
    /**
     * the function which start a action 
     *
     * @param [type] $req
     * @param [type] $res
     * @param [type] $next
     * @return void
     */
    public function handle($req, $res, $next)
    {
        if (is_array($this->callback)) {
            $reflect = new ReflectionClass($this->callback[0]);
            $classe = $this->callback[0];
            $funcName = $this->callback[1];
            if ($reflect->hasMethod($this->callback[1])) {
                return (new $classe())->$funcName($req, $res, $next);
            } else {
                $res->status(500)->send('
                
                <p style="margin: 100px 10%">
                    <b style="color: #ff000d;">Error</b>:  the controller <strong>' . $classe . '</strong> has not a method called : <strong>' . $funcName . '</strong>
                    <br>
                    <br>
                    <b style="color: tomato">Please fix this bug</b>
                </p>

                ');
            }
        } else if (is_callable($this->callback)) {
            $callback = $this->callback;
            return $callback($req, $res, $next);
        }
    }
}
