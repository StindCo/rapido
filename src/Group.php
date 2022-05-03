<?php

namespace StindCo\Rapido;

class Group
{
    protected $rootpath;
    public array $middlewares = [];

    public function __construct($rootpath, $router, $middleware)
    {
        $this->rootpath = $rootpath;
        $this->router   = $router;
    }

    public function use($middleware)
    {
        array_push($this->middlewares, $middleware);
        return $this;
    }

    /*
        ========================================================================
                            Routes Architecture
        =======================================================================
    */
    public function get($route, $callback, $commentaire = null)
    {
        if ($route == "/") $route = $this->rootpath;
        else {
            $route = $this->rootpath . $route;
        }
        $this->router->routes['get'][$route] = new Route($route, 'get',  $callback);
        $this->router->routes['get'][$route]->comment($commentaire);
        return $this;
    }
    public function post($route, $callback, $commentaire = null)
    {
        if ($route == "/") $route = $this->rootpath;
        else {
            $route = $this->rootpath . $route;
        }
        $this->router->routes['post'][$route] = new Route($route, 'post',  $callback);
        $this->router->routes['post'][$route]->comment($commentaire);
        return $this;
    }
    public function delete($route, $callback, $commentaire = null)
    {
        if ($route == "/") $route = $this->rootpath;
        $route = $this->rootpath . $route;

        $this->router->routes['delete'][$route] = new Route($route, 'delete',  $callback);
        $this->router->routes['delete'][$route]->comment($commentaire);
        return $this;
    }
    public function update($route, $callback, $commentaire = null)
    {
        if ($route == "/") $route = $this->rootpath;
        else {
            $route = $this->rootpath . $route;
        }
        $this->router->routes['update'][$route] = new Route($route, 'update',  $callback);
        $this->router->routes['update'][$route]->comment($commentaire);
        return $this;
    }
    public function put($route, $callback, $commentaire = null)
    {
        if ($route == "/") $route = $this->rootpath;
        else {
            $route = $this->rootpath . $route;
        }
        $this->router->routes['put'][$route] = new Route($route, 'put',  $callback);
        $this->router->routes['put'][$route]->comment($commentaire);
        return $this;
    }
    public function patch($route, $callback, $commentaire = null)
    {
        if ($route == "/") $route = $this->rootpath;
        else {
            $route = $this->rootpath . $route;
        }
        $this->router->routes['patch'][$route] = new Route($route, 'patch',  $callback);
        $this->router->routes['patch'][$route]->comment($commentaire);
        return $this;
    }
    public function where($array)
    {
        $method = array_key_last($this->router->routes);
        $route = array_key_last($this->router->routes[$method]);

        $this->router->addConditions($method, $route, $array);

        return $this->router;
    }
}
