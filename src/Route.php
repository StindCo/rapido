<?php

namespace StindCo\Rapido;

use ReflectionClass;

class Route
{
    public $path;
    public $method;
    public $name;
    public $callback;
    public $comment;

    public function __construct($path, $method, $callback)
    {
        $this->path = $path;
        $this->method = $method;

        $this->callback = $callback;
    }
    public function comment($commentaire)
    {
        $this->comment = $commentaire;
    }
    /**
     * the function which start a action 
     *
     * @param [type] $req
     * @param [type] $res
     * @param [type] $next
     * @return void
     */
    public function handle(Request $req, Response $res, $next)
    {
        if (is_array($this->callback)) {
            $reflect = new ReflectionClass($this->callback[0]);
            $classe = $this->callback[0];
            $funcName = $this->callback[1];
            if ($reflect->hasMethod($this->callback[1])) {
                return (new $classe($req, $res))->$funcName($req, $res, $next);
            } else {
                $res->status(404)->sendJson(["error" => "undefined method " . $funcName]);
            }
        } else if (is_callable($this->callback)) {
            $callback = $this->callback;
            return $callback($req, $res, $next);
        }
    }
}
