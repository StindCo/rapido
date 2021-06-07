<?php

namespace StindCo\Rapido;

use ArrayAccess;
use ReflectionObject;

class App implements ArrayAccess
{
    protected Configs $configs;
    protected Request $request;
    protected Response $response;
    protected Pipeline $pipeline;
    protected Middleware $middleware;
    protected array $env;

    public function __construct()
    {
        $this->configs = new Configs;
        $this->request = new Request();

        $this->response = new Response();
        $this->config($this->request->configs);

        $this->middleware = new Middleware($this->request, $this->response);
        $this->router = new Router($this->request, $this->response);
    }
    /*
        ==================================================================
                    Configurations for the application
        ==================================================================
    */
    /**
     * Configs
     *
     * @param String $key
     * @param mixed $value
     * @return $this
     */
    public function config($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $value) {
                $this->configs->set($k, $value);
            }
        } else {
            $this->configs->set($key, $value);
        }

        return $this;
    }
    public function getAllConfigurations()
    {
        return $this->configs->getAll();
    }

    private function configure($objects)
    {
        foreach ($objects->configs as $key => $value) {
            if (!is_null($this->configs->getAll()[$key])) {
                $objects->configs[$key] = $this->configs->getAll()[$key];
            }
        }
    }
    /*
        ======================================================================
                            Environnement's variables
        ======================================================================
    */
    public function setVar($key, $value): self
    {
        $this->env[$key] = $value;
        return $this;
    }
    public function getVar($key)
    {
        return $this->env[$key];
    }
    public function offsetGet($offset)
    {
        return $this->getVar($offset);
    }
    public function offsetSet($offset, $value)
    {
        $this->setVar($offset, $value);
    }
    public function offsetExists($offset)
    {
        return is_null($this->getVar($offset));
    }
    public function offsetUnset($offset)
    {
        unset($this->env[$offset]);
    }
    /*
        =======================================================================
                        Middleware Architecture Pipeline
        ======================================================================
    */
    /**
     * Add middleware 
     *
     * @param mixed $classe
     * @return self
     */
    public function use($classe): self
    {
        $this->middleware->pipe($classe);
        return $this;
    }

    /*
        ========================================================================
                            Routes Architecture
        =======================================================================
    */
    public function get($route, $callback)
    {
        $this->router->routes['get'][$route] = new Route($route, 'get',  $callback);
        return $this;
    }
    public function post($route, $callback)
    {
        $this->router->routes['post'][$route] = new Route($route, 'post',  $callback);
        return $this;
    }
    public function delete($route, $callback)
    {
        $this->router->routes['delete'][$route] = new Route($route, 'delete',  $callback);
        return $this;
    }
    public function update($route, $callback)
    {
        $this->router->routes['update'][$route] = new Route($route, 'update',  $callback);
        return $this;
    }
    public function put($route, $callback)
    {
        $this->router->routes['put'][$route] = new Route($route, 'put',  $callback);
        return $this;
    }
    public function patch($route, $callback)
    {
        $this->router->routes['patch'][$route] = new Route($route, 'patch',  $callback);
        return $this;
    }
    public function match(array $methods, $name, $callback)
    {
        foreach ($methods as $value) {
            $this->router->routes[strtolower($value)][$name] = new Route($name, strtolower($value),  $callback);
        }
    }
    public function where($array)
    {
        $method = array_key_last($this->router->routes);
        $route = array_key_last($this->router->routes[$method]);

        $this->router->addConditions($method, $route, $array);

        return $this->router;
    }
    public function otherwise($callback)
    {
        $this->router->routes['otherwise'] = new Route("othewise", "otherwise",  $callback);
        return $this;
    }




    public function run()
    {
        $this->router->set_configurations($this->configs->getAll());

        $this->configure($this->request);
        $this->configure($this->response);

        $pipeline = (new Pipeline($this->request, $this->response))
            ->pipe($this->middleware)
            ->pipe($this->router);
    }
}
