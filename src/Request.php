<?php

namespace StindCo\Rapido;

use Exception;

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

    public function query($key = null): Data | string | null
    {
        if (is_null($key)) {
            return (new Data())->setInformations($_GET);
        }
        return (new Data())->setInformations($_GET)->get($key);
    }

    public function form($key = null): Data | string | null
    {
        if (is_null($key)) {
            return (new Data())->setInformations($_POST);
        }
        return (new Data())->setInformations($_POST)->get($key);
    }

    public function files($key = null): Data | string | null
    {
        if (is_null($key)) {
            return (new Data())->setInformations($_FILES);
        }

        return (new Data())->setInformations($_FILES)->get($key);
    }

    public function input($key = null): Data | string | null
    {
        $data = file_get_contents("php://input");
        try {
            $data = json_decode($data, true);
        } catch (\Throwable $th) {
            throw new UserException("Error on your JSON data");
        }
        if (is_null($data)) throw new UserException("There's an error with your JSON.");

        if (is_null($key)) return (new Data())->setInformations($data);

        return (new Data())->setInformations($data)->get($key);
    }

    public function get_method()
    {
        return $this->get("REQUEST_METHOD");
    }
    public function get_url($route_mode)
    {
        if ($route_mode == '/') {
            $controller = explode("?", $this->get("REQUEST_URI"));
            return $controller[0];
        } else {
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
