<?php

namespace StindCo\Rapido;

use ArrayAccess;

/**
 * La classe App est la principale du projet 
 * Une fois instanciée, c'est dans cette classe que toute les processus de l'application pourront être lancer
 * @implements ArrayAccess
 * @see StindCo\Rapido
 */
class App
{
    /**
     * Cette propriété contient l'instance de la configuration
     * Configs est une classe permettant de configurer l'application
     *
     * @var Configs $configs
     */
    protected Configs $configs;
    /**
     * La propriété ayant l'instance Request
     *
     * @var Request
     */
    protected Request $request;
    /**
     * La propriété ayant l'instance de la Response
     *
     * @var Response
     */
    protected Response $response;
    /**
     * L'instance du Router
     * Le routeur est un Objet important dans le framework
     * C'est d'ailleurs l'une des principales partis du framework
     *
     * @var Router
     */
    protected Router $router;
    /**
     * L'instance de la pipeline
     * L'implémentation de la chaine de responsabilité
     *
     * @var Pipeline
     */
    protected Pipeline $pipeline;
    protected Middleware $middleware;
    protected array $env;
    protected array $groups = [];

    public function __construct()
    {
        $this->configs = new Configs;
        $this->request = new Request();

        $this->response = new Response();
        $this->config($this->request->configs);

        $this->middleware = new Middleware($this->request, $this->response);
        $this->router = new Router($this->request, $this->response);
    }

    public function get_version()
    {
        return "v1.0.0";
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
            if (array_key_exists($key, $this->configs->getAll())) {
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

    public function offsetExists(){
        
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
    public function get($route, $callback, $commentaire = null)
    {
        $this->router->routes['get'][$route] = new Route($route, 'get',  $callback);
        $this->router->routes['get'][$route]->comment($commentaire);
        return $this;
    }
    public function post($route, $callback, $commentaire = null)
    {
        $this->router->routes['post'][$route] = new Route($route, 'post',  $callback);
        $this->router->routes['post'][$route]->comment($commentaire);
        return $this;
    }
    public function delete($route, $callback, $commentaire = null)
    {
        $this->router->routes['delete'][$route] = new Route($route, 'delete',  $callback);
        $this->router->routes['delete'][$route]->comment($commentaire);
        return $this;
    }
    public function update($route, $callback, $commentaire = null)
    {
        $this->router->routes['update'][$route] = new Route($route, 'update',  $callback);
        $this->router->routes['update'][$route]->comment($commentaire);
        return $this;
    }
    public function put($route, $callback, $commentaire = null)
    {
        $this->router->routes['put'][$route] = new Route($route, 'put',  $callback);
        $this->router->routes['put'][$route]->comment($commentaire);
        return $this;
    }
    public function patch($route, $callback, $commentaire = null)
    {
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
    public function otherwise($callback)
    {
        $this->router->routes['otherwise'] = new Route("othewise", "otherwise",  $callback);
        return $this;
    }

    public function group($rootpath)
    {
        $this->groups[$rootpath] = new Group($rootpath, $this->router, $this->middleware);
        return $this->groups[$rootpath];
    }

    public function run()
    {
        $this->router->set_configurations($this->configs->getAll());
        $this->router->set_groups($this->groups);
        $this->configure($this->request);
        $this->configure($this->response);
        $this->information_route();

        $pipeline = (new Pipeline($this->request, $this->response))
            ->pipe($this->middleware)
            ->pipe($this->router);
    }

    public function information_route()
    {
        $req = $this->request;
        $res = $this->response;

        $this->get("/app_info", function ($req, $res, $next) {
            return $res->sendJson([
                "version" => $this->get_version(),
                "routes" => $this->router->getallroutes()
            ]);
        });
    }
}
