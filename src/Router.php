<?php

namespace StindCo\Rapido;

class Router extends Pipeline
{
    public array $routes = [
        'get' => []
    ];
    private array $conditions = [];
    private array $configs = [
        "route_mode" => "/"
    ];
    protected $groups;
    protected $middleware;

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
        $parameters = null;
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

                    if (!is_null($this->conditions[$method])) {
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
    public function getallroutes(): array
    {
        $arrays = $this->routes;

        return $arrays;
    }
    public function set_groups(array $groups)
    {
        $this->groups = $groups;
    }

    private function whats_groups($route)
    {
        if (is_null($route)) return null;

        foreach ($this->groups as $key => $value) {
            if (strpos($route->path, $key) === 0) {
                $groupe = $this->groups[$key];
                break;
            }
        }
        return $groupe;
    }

    /**
     * the function which used for start an action
     *
     * @param Request $req
     * @param Response $res
     * @param Callable $next
     * @return void
     */
    public function handle($req, $res, $next)
    {
        $this->method =  strtolower($req->get_method());
        $this->url = $req->get_url($this->getConf('route_mode')) ?? '/';

        $route = $this->path_resolver($this->method, $this->url);

        $this->pipe(function (Request $req, Response $res, $next) use ($route) {
            $group = $this->whats_groups($route);
            if (is_null($group) or $group->middlewares == []) return $next();
            $middleware = new Middleware($req, $res);
            for ($i = 0; $i < count($group->middlewares); $i++) {

                $continuation = $middleware->pipe($group->middlewares[$i]);
            }
            if ($continuation->canContinue()) return $next();
        })
            ->pipe(function ($req, $res, $next) use ($route) {
                if (($route) != NULL) {
                    $req->routeParams = $this->routeParams;
                    $req->route = $route;
                    return $route->handle($req, $res, $next);
                } else {
                    if (!is_null($this->routes['otherwise'])) {
                        return $this->routes['otherwise']->handle($req, $res, $next);
                    } else {
                        $res->status(404)->sendJson(["error" => "This route don't exits"]);
                    }
                }
                return $next();
            });
    }
}
