<?php

namespace StindCo\Rapido;

class Request extends Http
{
    public Route $route;
    public $configs = [
        "route_mode" => "/"
    ];
    public function __construct()
    {
        parent::__construct();
    }
    public function get($key)
    {
        return $this->DataSafeInformations[$key];
    }

    public function get_getDatas()
    {
        return (new Data())->setInformations($_GET);
    }

    public function get_postDatas()
    {
        return (new Data())->setInformations($_POST);
    }

    public function get_filesDatas()
    {
        return (new Data())->setInformations($_FILES);
    }

    public function get_putData()
    {
        
    }

    public function get_method()
    {
        return $this->get("REQUEST_METHOD");
    }
    public function get_url($route_mode)
    {
        if ($route_mode == '/') return $this->get("PATH_INFO");
        else {
            $controller = $_GET['component'];
            $action = $_GET["action"];
            $params = $_GET['params'];
            $url = "/{$controller}/{$action}/{$params}";
            if ($params == null or $params == "") $url = "/{$controller}/{$action}";
            if ($action == null or $action == "") {
                $url = "/{$controller}/.../{$params}";
                if ($params == null) $url = "/{$controller}";
            }
            if ($controller == "") $url = "/";

            return $url;
        }
    }
    public function get_root()
    {
        return $this->get("DOCUMENT_ROOT");
    }
    public function get_routeParams()
    {
        return $this->routeParams;
    }
}
