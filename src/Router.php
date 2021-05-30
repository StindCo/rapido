<?php

namespace StindCo\Rapido;

class Router extends PipeLine
{
    public array $routes = [];
    private array $conditions = [];
    private array $configs = [
        "route_mode" => "/"
    ];
    public function set_configurations(array $configs)
    {
        foreach ($configs as $key => $value) {
            if (key_exists($key, $this->configs)) {
                $this->configs[$key] = $value;
            } else continue;
        }
    }
    /**
     * fonction pour modifier une configuration
     *
     * @param string $a
     * @return void
     */
    private function getConf(string $a)
    {
        return $this->configs[$a];
    }
    public function addConditions($method, $route, $conditions)
    {
        $this->conditions[$method][$route] = $conditions;

        return $this;
    }
    /**
     * La fonction qui permet de traiter une url
     *
     * @param [type] $method
     * @param [type] $path
     * @return void
     */
    private function path_resolver($method, $path)
    {
        $urlData = explode("/", $path);
        foreach ($this->routes[$method] as $key => $value) {
            $routesData = explode("/", $key);
            if (count($routesData) == count($urlData)) {
                $ok = true;
                for ($i = 0; $i < count($urlData) && $ok == true; $i++) {
                    $params = explode(":", $routesData[$i]);
                    if (count($params) == 1) {
                        if ($urlData[$i] != $routesData[$i]) {
                            $ok = false;
                        }
                        continue;
                    } else {
                        $parameters[$params[1]] = $urlData[$i];
                        continue;
                    }
                }
                
                if ($ok == true) {
                    $conditions = true;

                    if(!is_null($this->conditions[$method])) {
                        if (key_exists($key, $this->conditions[$method])) {
                            foreach ($this->conditions[$method][$key] as $k => $condition) {
                                if (!preg_match($condition, $parameters[$k])) $conditions = false;
                                continue;
                            }
    
                            if ($conditions == false) {
                                continue;
                            }
                        }
                    }
                    
                    
                    $this->routeParams = $parameters;
                    $callback = $value;
                    break;
                }
            } else {
                continue;
            }

            if (count($routesData) == 1) {
                $callback = $this->routes[$method][$key];
                break;
            }
        }

        return $callback;
    }
    /**
     * the function which used for start an action
     *
     * @param [type] $req
     * @param [type] $res
     * @param [type] $next
     * @return void
     */
    public function handle($req, $res, $next)
    {
        $this->method =  strtolower($req->get_method());
        $this->url = $req->get_url($this->getConf('route_mode')) ?? '/';

        $route = $this->path_resolver($this->method, $this->url);
        
        if(($route) != NULL) {
            
            $req->routeParams = $this->routeParams;
            $req->route = $route;
            return $route->handle($req, $res, $next);
        } else {
            if(!is_null($this->routes['otherwise'])) {
                return $this->routes['otherwise']->handle($req, $res, $next);
            } else {
                $res->status(500)->send('
                
                <p style="margin: 100px 10%">
                    <b style="color: #ff000d;">Error</b>:  this route <strong>'. $this->url. '</strong> does not exist ... 
                    <br>
                    <br>
                    <b style="color: tomato">Please fix this bug</b>
                </p>

                ');
            }
            
        }
        return $next();
    }
}

